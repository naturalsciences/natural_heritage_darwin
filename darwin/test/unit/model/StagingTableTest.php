<?php
include(dirname(__FILE__).'/../../bootstrap/Doctrine.php');
$t = new lime_test(7, new lime_output_color());
$t->info("findLinked method");
$conn = Doctrine_Manager::connection();

$staging_ids = array();
$staging_ids_all = $conn->fetchAll("SELECT id FROM staging");
foreach ($staging_ids_all as $staging_id_val)
{
  $staging_ids[] = $staging_id_val['id'];
}

$s = Doctrine::getTable("Staging")->findLinked($staging_ids);

$t->is(count($s),1,'Number of distinct related template_table_record_ref records is well "1"');
$t->is(2,$s[0]['record_id'],'Staging record referenced is well "2"...');
$t->is(1,$s[0]['cnt'],'... and is well counted "1" time');

$conn
  ->execute("
              INSERT INTO staging_people (id, referenced_relation, record_id, formated_name, people_type)
              VALUES (2, 'staging', 2, 'Bricolux', 'preparator')
            ");

$conn
  ->execute("
              INSERT INTO codes (id, referenced_relation, record_id, code)
              VALUES (1024, 'staging', 1, 'Codex-1')
            ");

$s = Doctrine::getTable("Staging")->findLinked($staging_ids);

$t->is(count($s),2,'Number of distinct related template_table_record_ref records is well "2" now');
$t->is(1,$s[0]['cnt'],'The first one (record_id "1") is well counted "1" time...');
$t->is(2,$s[1]['cnt'],'... and the second one (record_id "2") is well counted "2" time (two staging_people entries)');
$t->is(2,$s[1]['record_id'],'Second one is well record_id "2" :)');

$conn->close();
