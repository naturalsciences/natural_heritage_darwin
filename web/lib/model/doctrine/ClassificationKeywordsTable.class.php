<?php
/**
 */
class ClassificationKeywordsTable extends DarwinTable
{
  public function findForTable($table_name, $record_id)
  {
     $q = Doctrine_Query::create()
      ->from('ClassificationKeywords c');
     $q = $this->addCatalogueReferences($q, $table_name, $record_id, 'c', true);
     $q->orderBy('c.keyword_type ASC');
     return $q->execute();
  }
}
