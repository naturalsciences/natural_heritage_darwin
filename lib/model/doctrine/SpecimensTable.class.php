<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SpecimensTable extends DarwinTable
{
    static public $acquisition_category = array(
        'Undefined' => 'Undefined',
        'Donation' => 'Donation',
        'Exchange' => 'Exchange',
        'Internal work' => 'Internal work',
        'Loan' => 'Loan',
        'Mission' => 'Mission',
        'Purchase' => 'Purchase',
        'seizure' => 'Judicial seizure',
        'Trip' => 'Trip',
        'Excavation' => 'Excavation',
        'Exploration' => 'Exploration',
        'Collect' => 'Collect',
        );

    /**
    * Get differents acquisition categories
    * @return array of key/value of acquisition categories
    */
    public static function getDistinctCategories()
    {
        try{
            $i18n_object = sfContext::getInstance()->getI18n();
        }
        catch( Exception $e )
        {
            return self::$acquisition_category;
        }
        return array_map(array($i18n_object, '__'), self::$acquisition_category);
    }

    /**
    * Get distinct tools
    * @return Doctrine_collection with distinct "tool" as column
    */
    public function getDistinctTools()
    {
        $results = Doctrine_Query::create()->
           select('DISTINCT(collecting_tool) as tool')->
           from('Specimens')->
           execute();
        return $results;
    }

    /**
    * Get distinct Method
    * @return Doctrine_collection with distinct "method" as column
    */
    public function getDistinctMethods()
    {
        $results = Doctrine_Query::create()->
           select('DISTINCT(collecting_method) as method')->
           from('Specimens')->
           execute();
        return $results;
    }
}
