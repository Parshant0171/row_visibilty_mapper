# RowVisibilityMapper

A package to apply filters at the backend while fetching data based on certain users fields.

## Config

Create a file in your App's 'config' called 'row-visbility-mapper.php' folder, and add the following code:

```
return [    
    'useTenants' => 0 //0 if no tenants, 1 if tenants are present.
];
//default 0. No config required if no tenants.
```

## Database Configuration

### Tables

The following tables are present for database configuration.
1. RVM Masters `rvm_masters`: Master (parent) configuration table to initiate a mapping request.
2. RVM Condition Fields `rvm_condition_fields`: BelongsTo RVM Master, and maps all user fields that may applied as filter while querying the model.
3. RVM Conditions `rvm_conditions`: BelongsTo Model being filtered and RVM Condition Field. Contains the `value` of the field to be applied as a filter during run-time.

### RVM Masters

| Property | Data Type | Description | Is Required |
|---|---|---|---|
| visibility_mappable_type | string | The model path of the model which will be using this package | Yes |
| allow_query_building_through_viewer | tinyint (bool) | If true, you can modify the Query Builder through the user model and apply custom filters | No. Default:0 |
| allow_query_building_through_model | tinyint (bool) | If true, you can modify the Query Builder through the  model (mappable) and apply custom filters | No. Default:0 |

### RVM Condition Fields

| Property | Data Type | Description | Is Required |
|---|---|---|---|
|rvm_master_id | int (relationship) | ID of RVM Master | Yes |
| field_name | string | Column Name of the variable in the user (or related user's) object which the code should fetch. Eg., if you need to fetch user's gender ID `user->gender_id`, insert `gender_id`; if you need to fetch user's gender name `user->gender->display_name`, insert `gender.display_name`. | Yes |
| uses_relationship | tinyint (bool) | if the field is fetched through a related table | No. Default 0 |
| field_fetch_method | json | How to fetch related fields. TBD: Add example JSONs | No |
| sequence | int | The sequnece in which the filters should be applied. All the filters in Sequnce #1 will be applied with an `AND` operator. Rest all sequences will be applied using an `OR` operator. | Yes |
| display_options | json | An options that you may need at the front end | No |

### RVM Conditions

| Property | Data Type | Description | Is Required |
|---|---|---|---|
| visibility_mappable | Polymorphic Relationship | Polymorphic Relationship with the model that needs to be filtered through the package | Yes |
| rvm_condition_field_id | int (relationship) | A relationship mapping of the `rvm_condition_fields` table. | Yes |
| rvm_value | string | The value to be applied at the time of filtering | Yes |

## Usage

Example:

Model to be filtered: `Jobs`

```
$user = User::find(1); //you current authenticated user.

$jobs = Jobs::fetchForUser($user); //will return a **query builder** with all the applied filters. You would need to call ->get() or ->paginate() as per your own requirements.
```

```
$user = User::find(1); //you current authenticated user.
$job = Job::find(1); //job selected by user.

$job->isUserEligible($user); //return bool
```

**In case of error, please check if property `error` is set on the returned value.**

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email karankumar@jgu.edu.in instead of using the issue tracker.