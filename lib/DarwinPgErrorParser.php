<?php
class DarwinPgErrorParser
{
  protected $nat_exception = null;

  protected static $errorRegexps = array(
    '/Author still used as author/i' => 'This Person is still referenced as an author',
    '/Impossible to impact children names/i' => 'Impossible to impact children names',
    '/Still Manager in some Collections/i' => 'This user is still Manager in some Collections',
    '/follow the rules of possible upper level attachement/i' => 'The modification does not follow the rules of Upper levels attachements',
    '/Update of unit level break "possible_upper_levels" rule of direct children related/i' => 'The modification does not follow the rules of Upper levels attachements',
    '/Impossible to update children Update of parent_ref/i' => 'Impossible to impact children names',
    '/Author must be defined as author/i' => 'The author must be defined as such to be attached',
    '/Experts must be defined as expert/i' =>  'The expert must be defined as such to be attached',
    '/Maximum number of renamed item reach/i' => 'Maximum number of "current names" reached',
    '/Maximum number of recombined item reach/i' => 'Maximum number of "recombination" reached',
    '/set this synonym twice/i' => 'You can\'t attach a synonym twice',
    '/\bunq_comments\b/' => 'You cannot add a particular comment notion twice',
    '/duplicate key value violates unique constraint "unq_specimens"/i' => 'This specimen already exists',
    '/duplicate key value violates unique constraint "unq_specimen_individuals"/i' => 'This individual already exists for this specimen',
    '/duplicate key value violates unique constraint "unq_properties"/i' => 'This property already exists',
    '/unq_collecting_methods/i' => 'This method already exists',
    '/unq_collecting_tools/i' => 'This tool already exist',
    '/violates check constraint ".*_minmax"/i' => 'Max count value must be superior or equal to Min count value',
    '/violates check constraint ".*_min"/i' => 'Min count value must be a positive number',
    '/violates check constraint "chk_collecting_methods_method"/i' => 'Method inserted cannot be empty',
    '/violates check constraint "chk_collecting_tools_tool"/i' => 'Tool inserted cannot be empty',
    '/unq_users/i' => 'This user already exists',
    '/\bchk_not_related_to_self\b/' => 'You cannot recombine it to itself',
    '/\bYou don\'t have the rights to insert into or update a specimen in this collection\b/' => 'You don\'t have the rights to insert into or update a specimen in this collection',
    '/\bchk_chk_possible_upper_level_taxa\b/' => 'The parenty does not follow the possible upper level rule',
    '/\bunq_catalogue_people\b/' => 'This person already has this role',
    '/\bunq_catalogue_relationships\b/' => 'This relationship already exists',
    '/\bunq_chronostratigraphy\b/' => 'This Chrono unit already exists',
    '/\bunq_lithostratigraphy\b/' => 'This Litho unit already exists',
    '/\bunq_lithology\b/' => 'This Lithologic unit already exists',
    '/\bunq_mineralogy\b/' => 'This Mineralogic unit already exists',
    '/\bunq_expedition\b/' => 'This Expedition already exists',
    '/\bunq_synonym\b/' => 'This synonym already exist',
    '/\bunq_codes\b/' => 'This code already exists',
    '/\bunq_collections\b/' => 'This collection already exists',
    '/\bunq_ext_links\b/' => 'This External link is already set',
    '/\bunq_identifications\b/' => 'You cannot set the same identification twice',
    '/\bunq_igs\b/' => 'This I.G. already exists',
    '/\bunq_specimen_parts_insurances\b/' => 'This insurance already exists',
    '/\bunq_my_saved_searches\b/' => 'You\'ve already save a search with this name',
    '/\buunq_my_saved_specimens\b/' => 'You\'ve already save a list of specimen with this name',
    '/\bunq_people\b/' => 'This people already exists',
    '/\buunq_specimens_accompanying\b/' => 'This accompanying element already exists',
    '/\bunq_tag_groups\b/' => 'This tag group already exists',
    '/\bunq_igs\b/' => 'This I.G. already exists',
    '/\bunq_users_login_infos_user_name\b/' => 'This user login already exists',
    '/\bunq_users_login_infos\b/' => 'This user login already exists',
    '/\bunq_vernacular_names\b/' => 'This vernacular name already exists',
    '/\bfct_cpy_fulltoindex\b/' => 'There is a problem with a special character. Please contact the administrators.',
    '/\bgenders_chk\b/' => 'The gender can only be M or F',
    '/\bunq_catalogue_levels\b/' => 'This catalogue level already exists',
    '/\bunq_possible_upper_levels\b/' => 'This upper level already exists',
    '/\bunq_comments\b/' => 'You cannot write a comment for the same notion twice (write on the one already created)',
    '/\bunq_vernacular_names\b/' => 'This vernacular name already exists',
    '/\bunq_users\b/' => 'This user allreay exists',
    '/\bunq_people_languages\b/' => 'You have already set this language for this person',
    '/\bunq_users_languages\b/' => 'You have already set this language for this user',
    '/\bunq_collections_rights\b/' => 'This user already has rights on this collection',
    '/\bunq_my_saved_searches\b/' => 'You have already have a saved search with this name',
    '/\bunq_my_widgets\b/' => 'This user can have this widget twice',
    '/\bunq_taxonomy\b/' => 'This taxonomy already exists',
    '/\buniq_words\b/' => 'This word already exists',
    '/\bunq_loan_items\b/' => 'You have 2 similar loan items',
    '/\bfk_people_relationships_people_02\b/' => 'This persons record still has links with others',
    '/\bfk_collections_institutions\b/' => 'This institution still has collections',
    '/\bfk_collections_users\b/' => 'This user is collection manager',
    '/\bfk_taxonomy_level_ref_catalogue_levels\b/' => 'This level is used in taxonomy',
    '/\bfk_chronostratigraphy_level_ref_catalogue_levels\b/' => 'This level is used in chronostratigraphy',
    '/\bfk_lithostratigraphy_level_ref_catalogue_levels\b/' => 'This level is used in lithostratigraphy',
    '/\bfk_mineralogy_level_ref_catalogue_levels\b/' => 'This level is used in mineralogy',
    '/\bfk_lithology_level_ref_catalogue_levels\b/' => 'This level is used in lithology',                
    '/\bfk_specimens_expeditions\b/' => 'This expedition is linked with one or more specimens',
    '/\bfk_specimens_igs\b/' => 'This I.G. number is linked with one or more specimens',
    '/\bfk_specimens_gtu\b/' => 'This gtu is linked with one or more specimens',
    '/\bfk_specimens_collections\b/' => 'This collection is linked with one or more specimens',
    '/\bfk_specimens_taxonomy\b/' => 'This taxon is linked with one or more specimens',
    '/\bfk_specimens_lithostratigraphy\b/' => 'This lithostratigraphical unit is linked with one or more specimens',
    '/\bfk_specimens_lithology\b/' => 'This lithological unit is linked with one or more specimens',
    '/\bfk_specimens_mineralogy\b/' => 'This mineralogical unit is linked with one or more specimens',
    '/\bfk_specimens_chronostratigraphy\b/' => 'This chronostratigraphical unit is linked with one or more specimens',
    '/\bfk_specimens_host_taxonomy\b/' => 'This taxon is referenced as host in one or more specimens',            
    '/\bfk_specimens_host_specimen\b/' => 'This specimen is referenced as host in one or more specimens',    
    '/\bfk_specimens_accompanying_mineralogy\b/' => 'This mineral is referenced as accompanying element',
    '/\bfk_specimens_accompanying_taxonomy\b/' => 'This taxon is referenced as accompanying element',
    '/\bunq_staging_tag_groups\b/' => 'This tag group already exists',
  );

  public function __construct(Doctrine_Exception $e)
  {
    $this->nat_exception = $e;
  }

  public function getMessage()
  {    
    $original_message = $this->nat_exception->getMessage();
    foreach (self::$errorRegexps as $regexp => $message)
    {
	    if(preg_match($regexp, $original_message))
	    {
	        return $message;
	    }
    }
    return 'Unknown Error : '.$original_message;
  }
}
