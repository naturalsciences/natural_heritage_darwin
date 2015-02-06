select y.modification_date_time as modification_date_time, y.user_ref as user_ref, y.action as action, y.newkey as hkey, x.oldvalue as old_value, y.newvalue as new_value
from
(
  select user_ref, action, (each(old_value)).key as oldkey, (each(old_value)).value as oldvalue, modification_date_time 
  from users_tracking
  where referenced_relation = 'specimens'
    and record_id =  793264
    and action = 'update'
  order by modification_date_time desc
) as x
inner join
(
  select user_ref, action, (each(new_value)).key as newkey, (each(new_value)).value as newvalue, modification_date_time 
  from users_tracking
  where referenced_relation = 'specimens'
    and record_id = 793264
    and action = 'update'
  order by modification_date_time desc
) as y
on x.oldkey = y.newkey and x.oldvalue != y.newvalue and x.modification_date_time = y.modification_date_time;
