<?php

/**
 * BaseDoctrineTemporalInformationGtuGroupUnnestTags
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $code
 * @property integer $gtu_from_date_mask
 * @property string $gtu_from_date
 * @property integer $gtu_to_date_mask
 * @property string $gtu_to_date
 * @property float $latitude
 * @property float $longitude
 * @property string $location
 * @property float $lat_long_accuracy
 * @property float $elevation
 * @property float $elevation_accuracy
 * @property integer $import_ref
 * @property string $collector_refs
 * @property string $expedition_refs
 * @property integer $collection_ref
 * @property string $coordinates_source
 * @property integer $latitude_dms_degree
 * @property float $latitude_dms_minutes
 * @property float $latitude_dms_seconds
 * @property integer $latitude_dms_direction
 * @property integer $longitude_dms_degree
 * @property float $longitude_dms_minutes
 * @property float $longitude_dms_seconds
 * @property integer $longitude_dms_direction
 * @property float $latitude_utm
 * @property float $longitude_utm
 * @property string $utm_zone
 * @property integer $from_date_mask
 * @property string $from_date
 * @property integer $to_date_mask
 * @property string $to_date
 * @property string $comments
 * @property string $properties
 * @property string $tag
 * @property string $tag_indexed
 * @property Doctrine_Collection $TagGroups
 * @property Doctrine_Collection $Tags
 * 
 * @method integer                                       getId()                      Returns the current record's "id" value
 * @method string                                        getCode()                    Returns the current record's "code" value
 * @method integer                                       getGtuFromDateMask()         Returns the current record's "gtu_from_date_mask" value
 * @method string                                        getGtuFromDate()             Returns the current record's "gtu_from_date" value
 * @method integer                                       getGtuToDateMask()           Returns the current record's "gtu_to_date_mask" value
 * @method string                                        getGtuToDate()               Returns the current record's "gtu_to_date" value
 * @method float                                         getLatitude()                Returns the current record's "latitude" value
 * @method float                                         getLongitude()               Returns the current record's "longitude" value
 * @method string                                        getLocation()                Returns the current record's "location" value
 * @method float                                         getLatLongAccuracy()         Returns the current record's "lat_long_accuracy" value
 * @method float                                         getElevation()               Returns the current record's "elevation" value
 * @method float                                         getElevationAccuracy()       Returns the current record's "elevation_accuracy" value
 * @method integer                                       getImportRef()               Returns the current record's "import_ref" value
 * @method string                                        getCollectorRefs()           Returns the current record's "collector_refs" value
 * @method string                                        getExpeditionRefs()          Returns the current record's "expedition_refs" value
 * @method integer                                       getCollectionRef()           Returns the current record's "collection_ref" value
 * @method string                                        getCoordinatesSource()       Returns the current record's "coordinates_source" value
 * @method integer                                       getLatitudeDmsDegree()       Returns the current record's "latitude_dms_degree" value
 * @method float                                         getLatitudeDmsMinutes()      Returns the current record's "latitude_dms_minutes" value
 * @method float                                         getLatitudeDmsSeconds()      Returns the current record's "latitude_dms_seconds" value
 * @method integer                                       getLatitudeDmsDirection()    Returns the current record's "latitude_dms_direction" value
 * @method integer                                       getLongitudeDmsDegree()      Returns the current record's "longitude_dms_degree" value
 * @method float                                         getLongitudeDmsMinutes()     Returns the current record's "longitude_dms_minutes" value
 * @method float                                         getLongitudeDmsSeconds()     Returns the current record's "longitude_dms_seconds" value
 * @method integer                                       getLongitudeDmsDirection()   Returns the current record's "longitude_dms_direction" value
 * @method float                                         getLatitudeUtm()             Returns the current record's "latitude_utm" value
 * @method float                                         getLongitudeUtm()            Returns the current record's "longitude_utm" value
 * @method string                                        getUtmZone()                 Returns the current record's "utm_zone" value
 * @method integer                                       getFromDateMask()            Returns the current record's "from_date_mask" value
 * @method string                                        getFromDate()                Returns the current record's "from_date" value
 * @method integer                                       getToDateMask()              Returns the current record's "to_date_mask" value
 * @method string                                        getToDate()                  Returns the current record's "to_date" value
 * @method string                                        getComments()                Returns the current record's "comments" value
 * @method string                                        getProperties()              Returns the current record's "properties" value
 * @method string                                        getTag()                     Returns the current record's "tag" value
 * @method string                                        getTagIndexed()              Returns the current record's "tag_indexed" value
 * @method Doctrine_Collection                           getTagGroups()               Returns the current record's "TagGroups" collection
 * @method Doctrine_Collection                           getTags()                    Returns the current record's "Tags" collection
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setId()                      Sets the current record's "id" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setCode()                    Sets the current record's "code" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setGtuFromDateMask()         Sets the current record's "gtu_from_date_mask" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setGtuFromDate()             Sets the current record's "gtu_from_date" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setGtuToDateMask()           Sets the current record's "gtu_to_date_mask" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setGtuToDate()               Sets the current record's "gtu_to_date" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatitude()                Sets the current record's "latitude" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLongitude()               Sets the current record's "longitude" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLocation()                Sets the current record's "location" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatLongAccuracy()         Sets the current record's "lat_long_accuracy" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setElevation()               Sets the current record's "elevation" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setElevationAccuracy()       Sets the current record's "elevation_accuracy" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setImportRef()               Sets the current record's "import_ref" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setCollectorRefs()           Sets the current record's "collector_refs" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setExpeditionRefs()          Sets the current record's "expedition_refs" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setCollectionRef()           Sets the current record's "collection_ref" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setCoordinatesSource()       Sets the current record's "coordinates_source" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatitudeDmsDegree()       Sets the current record's "latitude_dms_degree" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatitudeDmsMinutes()      Sets the current record's "latitude_dms_minutes" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatitudeDmsSeconds()      Sets the current record's "latitude_dms_seconds" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatitudeDmsDirection()    Sets the current record's "latitude_dms_direction" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLongitudeDmsDegree()      Sets the current record's "longitude_dms_degree" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLongitudeDmsMinutes()     Sets the current record's "longitude_dms_minutes" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLongitudeDmsSeconds()     Sets the current record's "longitude_dms_seconds" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLongitudeDmsDirection()   Sets the current record's "longitude_dms_direction" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLatitudeUtm()             Sets the current record's "latitude_utm" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setLongitudeUtm()            Sets the current record's "longitude_utm" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setUtmZone()                 Sets the current record's "utm_zone" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setFromDateMask()            Sets the current record's "from_date_mask" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setFromDate()                Sets the current record's "from_date" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setToDateMask()              Sets the current record's "to_date_mask" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setToDate()                  Sets the current record's "to_date" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setComments()                Sets the current record's "comments" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setProperties()              Sets the current record's "properties" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setTag()                     Sets the current record's "tag" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setTagIndexed()              Sets the current record's "tag_indexed" value
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setTagGroups()               Sets the current record's "TagGroups" collection
 * @method DoctrineTemporalInformationGtuGroupUnnestTags setTags()                    Sets the current record's "Tags" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDoctrineTemporalInformationGtuGroupUnnestTags extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('doctrine_temporal_information_gtu_group_unnest_tags');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('code', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('gtu_from_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('gtu_from_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '0001-01-01',
             ));
        $this->hasColumn('gtu_to_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('gtu_to_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '2038-12-31',
             ));
        $this->hasColumn('latitude', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('longitude', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('location', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('lat_long_accuracy', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('elevation', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('elevation_accuracy', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('import_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('collector_refs', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('expedition_refs', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collection_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('coordinates_source', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('latitude_dms_degree', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('latitude_dms_minutes', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('latitude_dms_seconds', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('latitude_dms_direction', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('longitude_dms_degree', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('longitude_dms_minutes', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('longitude_dms_seconds', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('longitude_dms_direction', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('latitude_utm', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('longitude_utm', 'float', null, array(
             'type' => 'float',
             ));
        $this->hasColumn('utm_zone', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('from_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('from_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '0001-01-01',
             ));
        $this->hasColumn('to_date_mask', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('to_date', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'default' => '2038-12-31',
             ));
        $this->hasColumn('comments', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('properties', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('tag', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('tag_indexed', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('TagGroups', array(
             'local' => 'id',
             'foreign' => 'gtu_ref'));

        $this->hasMany('Tags', array(
             'local' => 'id',
             'foreign' => 'gtu_ref'));
    }
}