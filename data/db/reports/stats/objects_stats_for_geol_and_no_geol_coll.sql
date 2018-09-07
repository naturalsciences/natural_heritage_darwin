select min('Geological and Paleontological Collections') as title, sum(specimen_count_min) as minimum, sum(specimen_count_max) as maximum
from specimens
where collection_ref in (11,12,269,270,265,242,260,239,233,277,234,232,241,271,262,275,273,274,276,272,263,289,200,305,281,280,279,231,3,288,235,201,307,321)
union
select min('Zoological collections') as title, sum(specimen_count_min) as minimum, sum(specimen_count_max) as maximum
from specimens
where collection_ref not in (11,12,269,270,265,242,260,239,233,277,234,232,241,271,262,275,273,274,276,272,263,289,200,305,281,280,279,231,3,288,235,201,307,321, 316, 315, 319, 322);
