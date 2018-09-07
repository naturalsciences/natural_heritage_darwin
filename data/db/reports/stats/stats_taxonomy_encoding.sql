/* SQL to get the encoding stats for a given user for taxonomy */
select distinct referenced_relation, action, count(action) over (partition by referenced_relation, action)
from users_tracking
where user_ref = 40915 -- replace by the wished user name
 and referenced_relation in ('taxonomy', 'classification_synonymies', 'classification_keywords')
 and modification_date_time between '2017-02-14 00:00:01'::timestamp and '2017-02-14 23:59:59'::timestamp
order by referenced_relation, action;