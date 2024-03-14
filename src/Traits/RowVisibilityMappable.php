<?php

namespace Jgu\RowVisibilityMapper\Traits;

use Jgu\RowVisibilityMapper\Models\RvmCondition;
use Jgu\RowVisibilityMapper\Models\RvmConditionField;
use Jgu\RowVisibilityMapper\Models\RvmMaster;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Jgu\RowVisibilityMapper\Utils\ErrorUtils;

trait RowVisibilityMappable {

    public function rvmGetClassName(){
        return $this->className ?? get_class();
    }

    public function rvmDumps($data){
        echo json_encode($data);
    }

    private function fetchRvmMaster(){
        return RvmMaster::where('visibility_mappable_type', $this->rvmGetClassName())->first();
    }

    public function rvmConditions(){
        return $this->morphMany(RvmCondition::class, 'visibility_mappable');    
    }

    private static function recursiveParse($key, $values): array{
        $val = [];
        foreach($values as $value){            
            $var = $value->$key;
            if(is_a($var, 'Illuminate\Database\Eloquent\Collection')){                
                foreach($var as $v)
                    $val[] = $v;
            }else{
                $val[] = $var;
            }
            
        }
        return $val;
    }

    private static function fetchUserRelatedField(string $through, string $fieldName, $user){
        $user->load($through);
        $values = [];
        $throughSplit = explode(".", $through);
        if(sizeof($throughSplit)==0){
            return ErrorUtils::renderCommonError('CONFIG_ERROR', ['description' => 'JSON Data Configuration Error.']);    
        }        
        $firstKey = $throughSplit[0];
        
        $parsing = $user->$firstKey;  
        
        foreach($throughSplit as $key => $element){            
            //user.userRoles.role_id
            if ($key != array_key_first($throughSplit)) {                
                $parsing = self::recursiveParse($element, $parsing);    
                
            }            
            
            if ($key === array_key_last($throughSplit)) {                
                return self::recursiveParse($fieldName, $parsing);
            }
            
        }                
    }

    private static function fetchUserField(RvmConditionField $rvmField, $user) {
        if($rvmField){
            if($rvmField->uses_relationship == 1){
                $fetchMethod = json_decode($rvmField->field_fetch_method);    
                $values = self::fetchUserRelatedField($fetchMethod->through, $rvmField->field_name, $user);
                if($values and isset($values->error))
                    return $values;
                return ['method' => "whereIn", 'val' => $values];
            }else{
                $fieldKey = $rvmField->field_name;
                return ['method' => "where", 'val' => $user->$fieldKey];
            }
            
        }
        return ErrorUtils::renderCommonError('CONFIG_ERROR', ['description' => 'Error in data mapping for user fields.']);    
    }
    

    private static function rvmConditionQueryMaker(Builder $queryBuilder, Collection $conditionFields, $user, bool $isFirst, int $modelId=0): Builder{   
        $method = $isFirst ? "whereHas" : "orWhereHas";
        foreach($conditionFields as $rvmField){
            $queryBuilder->$method('rvmConditions', function (Builder $query) use ($rvmField, $user, $modelId) {                                
                $q = self::fetchUserField($rvmField, $user);
                $methodQ = $q['method'];
                if($q && !isset($q->error)){
                    if($modelId!==0) $query->where('visibility_mappable_id', $modelId);
                    $query->where('rvm_condition_field_id', '=', $rvmField->id)
                        ->$methodQ('rvm_value', $q['val']);                
                }else{
                    return $q;
                }                            
            });        
        }
        return $queryBuilder;
    }

    public static function fetchForUser($user, int $modelId = 0): Builder{            
        $queryBuilder = $modelId === 0 ? self::where('id', '>', 0) : self::where('id','=', $modelId);        
        // $queryBuilder = self::where('id', '>', 0);
        $rvm = RvmMaster::where('visibility_mappable_type', get_class())->first();        
        $currentSequence = 1;
        if($rvm && $rvm->rvmFields){
            while(true){
                $sequenceFields = $rvm->rvmFields->where('sequence', $currentSequence);
                
                if(sizeof($sequenceFields) == 0)
                    break;
                
                $queryBuilder = self::rvmConditionQueryMaker($queryBuilder, $sequenceFields, $user, $currentSequence == 1, $modelId);                                
                if($queryBuilder && isset($queryBuilder->error)){
                    return $queryBuilder;
                }                
                $currentSequence++;
            }
        }else{
            return ErrorUtils::renderCommonError('CONFIG_ERROR', ['description' => 'RVM Condition Fields not found.']);
        }
        return $queryBuilder;
    }
    
    public function isUserEligible($user){
        $builder = self::fetchForUser($user, $this->id);     
        if($builder && isset($builder->error)){
            return $builder;
        }                           
        $data = $builder->get();
        return sizeof($data) === 0 ? false : true;
    }

    public static function getRvmConditionData($visibility_mappable_type,$visibility_mappable_id) {
        
        $rvmConditions = RvmCondition::where(["visibility_mappable_type" => $visibility_mappable_type,"visibility_mappable_id" => $visibility_mappable_id])->with('rvmFields')->get();
        
        $data = [];

        foreach($rvmConditions as $rvmCondition){
            $throughRelationship = $rvmCondition->rvmFields->field_fetch_method;
            $relationshipJSON = json_decode($throughRelationship,TRUE);
            $finalModelPath = $relationshipJSON['final_destination_model_name'];

            $finalData = $finalModelPath::where('id',$rvmCondition->rvm_value)->first();
            array_push($data,[$finalData,$rvmCondition->id]);
        }

        return $data;
    }

}