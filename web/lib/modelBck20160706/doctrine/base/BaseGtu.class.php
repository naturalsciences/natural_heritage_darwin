<?php

/**
 * BaseGtu
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
 * @property varchar $utm_zone
 * @property string $location
 * @property float $lat_long_accuracy
 * @property float $elevation
 * @property float $elevation_accuracy
 * @property string $elevation_unit
 * @property Doctrine_Collection $TagGroups
 * @property Doctrine_Collection $Tags
 * @property Doctrine_Collection $Specimens
 * 
 * @method integer             getId()                      Returns the current record's "id" value
 * @method string              getCode()                    Returns the current record's "code" value
 * @method integer             getGtuFromDateMask()         Returns the current record's "gtu_from_date_mask" value
 * @method string              getGtuFromDate()             Returns the current record's "gtu_from_date" value
 * @method integer             getGtuToDateMask()           Returns the current record's "gtu_to_date_mask" value
 * @method string              getGtuToDate()               Returns the current record's "gtu_to_date" value
 * @method float               getLatitude()                Returns the current record's "latitude" value
 * @method float               getLongitude()               Returns the current record's "longitude" value
 * @method string              getCoordinatesSource()       Returns the current record's "coordinates_source" value
 * @method integer             getLatitudeDmsDegree()       Returns the current record's "latitude_dms_degree" value
 * @method float               getLatitudeDmsMinutes()      Returns the current record's "latitude_dms_minutes" value
 * @method float               getLatitudeDmsSeconds()      Returns the current record's "latitude_dms_seconds" value
 * @method integer             getLatitudeDmsDirection()    Returns the current record's "latitude_dms_direction" value
 * @method integer             getLongitudeDmsDegree()      Returns the current record's "longitude_dms_degree" value
 * @method float               getLongitudeDmsMinutes()     Returns the current record's "longitude_dms_minutes" value
 * @method float               getLongitudeDmsSeconds()     Returns the current record's "longitude_dms_seconds" value
 * @method integer             getLongitudeDmsDirection()   Returns the current record's "longitude_dms_direction" value
 * @method float               getLatitudeUtm()             Returns the current record's "latitude_utm" value
 * @method float               getLongitudeUtm()            Returns the current record's "longitude_utm" value
 * @method varchar             getUtmZone()                 Returns the current record's "utm_zone" value
 * @method string              getLocation()                Returns the current record's "location" value
 * @method float               getLatLongAccuracy()         Returns the current record's "lat_long_accuracy" value
 * @method float               getElevation()               Returns the current record's "elevation" value
 * @method float               getElevationAccuracy()       Returns the current record's "elevation_accuracy" value
 * @method string              getElevationUnit()           Returns the current record's "elevation_unit" value
 * @method Doctrine_Collection getTagGroups()               Returns the current record's "TagGroups" collection
 * @method Doctrine_Collection getTags()                    Returns the current record's "Tags" collection
 * @method Doctrine_Collection getSpecimens()               Returns the current record's "Specimens" collection
 * @method Gtu                 setId()                      Sets the current record's "id" value
 * @method Gtu                 setCode()                    Sets the current record's "code" value
 * @method Gtu                 setGtuFromDateMask()         Sets the current record's "gtu_from_date_mask" value
 * @method Gtu                 setGtuFromDate()             Sets the current record's "gtu_from_date" value
 * @method Gtu                 setGtuToDateMask()           Sets the current record's "gtu_to_date_mask" value
 * @method Gtu                 setGtuToDate()               Sets the current record's "gtu_to_date" value
 * @method Gtu                 setLatitude()                Sets the current record's "latitude" value
 * @method Gtu                 setLongitude()               Sets the current record's "longitude" value
 * @method Gtu                 setCoordinatesSource()       Sets the current record's "coordinates_source" value
 * @method Gtu                 setLatitudeDmsDegree()       Sets the current record's "latitude_dms_degree" value
 * @method Gtu                 setLatitudeDmsMinutes()      Sets the current record's "latitude_dms_minutes" value
 * @method Gtu                 setLatitudeDmsSeconds()      Sets the current record's "latitude_dms_seconds" value
 * @method Gtu                 setLatitudeDmsDirection()    Sets the current record's "latitude_dms_direction" value
 * @method Gtu                 setLongitudeDmsDegree()      Sets the current record's "longitude_dms_degree" value
 * @method Gtu                 setLongitudeDmsMinutes()     Sets the current record's "longitude_dms_minutes" value
 * @method Gtu                 setLongitudeDmsSeconds()     Sets the current record's "longitude_dms_seconds" value
 * @method Gtu                 setLongitudeDmsDirection()   Sets the current record's "longitude_dms_direction" value
 * @method Gtu                 setLatitudeUtm()             Sets the current record's "latitude_utm" value
 * @method Gtu                 setLongitudeUtm()            Sets the current record's "longitude_utm" value
 * @method Gtu                 setUtmZone()                 Sets the current record's "utm_zone" value
 * @method Gtu                 setLocation()                Sets the current record's "location" value
 * @method Gtu                 setLatLongAccuracy()         Sets the current record's "lat_long_accuracy" value
 * @method Gtu                 setElevation()               Sets the current record's "elevation" value
 * @method Gtu                 setElevationAccuracy()       Sets the current record's "elevation_accuracy" value
 * @method Gtu                 setElevationUnit()           Sets the current record's "elevation_unit" value
 * @method Gtu                 setTagGroups()               Sets the current record's "TagGroups" collection
 * @method Gtu                 setTags()                    Sets the current record's "Tags" collection
 * @method Gtu                 setSpecimens()               Sets the current record's "Specimens" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseGtu extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('gtu');
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
        $this->hasColumn('utm_zone', 'varchar', null, array(
             'type' => 'varchar',
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
        $this->hasColumn('elevation_unit', 'string', null, array(
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

        $this->hasMany('Specimens', array(
             'local' => 'id',
             'foreign' => 'gtu_ref'));
    }
}