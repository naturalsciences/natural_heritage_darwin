<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseTaxonomy extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('taxonomy');
        $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', null, array('type' => 'string', 'notnull' => true));
        $this->hasColumn('name_indexed', 'string', null, array('type' => 'string'));
        $this->hasColumn('description_year', 'integer', null, array('type' => 'integer'));
        $this->hasColumn('description_year_compl', 'string', 2, array('type' => 'string', 'length' => '2'));
        $this->hasColumn('level_ref', 'integer', null, array('type' => 'integer'));
        $this->hasColumn('status', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => 'valid'));
        $this->hasColumn('path', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => '/'));
        $this->hasColumn('parent_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('domain_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('domain_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('kingdom_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('kingdom_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_phylum_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_phylum_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('phylum_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('phylum_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_phylum_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_phylum_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_phylum_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_phylum_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_cohort_botany_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_cohort_botany_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('cohort_botany_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('cohort_botany_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_cohort_botany_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_cohort_botany_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_cohort_botany_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_cohort_botany_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_class_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_class_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('class_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('class_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_class_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_class_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_class_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_class_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_division_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_division_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('division_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('division_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_division_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_division_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_division_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_division_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_legion_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_legion_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('legion_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('legion_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_legion_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_legion_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_legion_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_legion_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_cohort_zoology_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_cohort_zoology_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('cohort_zoology_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('cohort_zoology_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_cohort_zoology_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_cohort_zoology_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_cohort_zoology_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_cohort_zoology_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_order_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_order_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('order_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('order_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_order_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_order_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_order_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_order_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('section_zoology_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('section_zoology_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_section_zoology_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_section_zoology_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_family_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_family_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('family_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('family_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_family_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_family_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_family_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_family_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_tribe_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_tribe_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('tribe_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('tribe_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_tribe_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_tribe_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('infra_tribe_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('infra_tribe_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('genus_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('genus_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_genus_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_genus_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('section_botany_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('section_botany_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_section_botany_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_section_botany_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('serie_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('serie_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_serie_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_serie_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('super_species_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('super_species_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('species_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('species_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_species_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_species_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('variety_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('variety_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_variety_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_variety_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('form_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('form_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('sub_form_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('sub_form_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('abberans_ref', 'integer', null, array('type' => 'integer', 'notnull' => true, 'default' => 0));
        $this->hasColumn('abberans_indexed', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => ''));
        $this->hasColumn('chimera_hybrid_pos', 'string', null, array('type' => 'string', 'notnull' => true, 'default' => 'none'));
        $this->hasColumn('extinct', 'boolean', null, array('type' => 'boolean', 'notnull' => true, 'default' => false));
    }

    public function setUp()
    {
        $this->hasOne('Taxonomy as Parent', array('local' => 'parent_ref',
                                                  'foreign' => 'id'));

        $this->hasMany('Taxonomy', array('local' => 'id',
                                         'foreign' => 'parent_ref'));

        $this->hasMany('Soortenregister', array('local' => 'id',
                                                'foreign' => 'taxa_ref'));

        $this->hasMany('Specimens', array('local' => 'id',
                                          'foreign' => 'taxon_ref'));

        $this->hasMany('SpecimensAccompanying', array('local' => 'id',
                                                      'foreign' => 'taxon_ref'));
    }
}