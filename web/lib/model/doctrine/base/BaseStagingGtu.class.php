<?php

/**
 * BaseStagingGtu
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $import_ref
 * @property string $status
 * @property boolean $date_included
 * @property boolean $tags_merged
 * @property boolean $sensitive_information_withheld
 * @property integer $gtu_ref
 * @property string $station_type
 * @property string $sampling_code
 * @property string $sampling_field_number
 * @property string $event_cluster_code
 * @property string $event_order
 * @property string $ig_num
 * @property string $ig_num_indexed
 * @property string $collections
 * @property string $collectors
 * @property string $expeditions
 * @property string $collection_refs
 * @property string $collector_refs
 * @property string $expedition_refs
 * @property string $iso3166
 * @property string $iso3166_subdivision
 * @property string $countries
 * @property string $tags
 * @property string $tags_indexed
 * @property string $locality_text
 * @property string $locality_text_indexed
 * @property string $ecology_text
 * @property string $ecology_text_indexed
 * @property string $coordinates_format
 * @property string $latitude1
 * @property string $longitude1
 * @property string $latitude2
 * @property string $longitude2
 * @property string $gis_type
 * @property string $coordinates_wkt
 * @property string $coordinates_datum
 * @property integer $coordinates_proj_ref
 * @property string $coordinates_original
 * @property decimal $coordinates_accuracy
 * @property string $coordinates_accuracy_text
 * @property decimal $station_baseline_elevation
 * @property decimal $station_baseline_accuracy
 * @property decimal $sampling_elevation_start
 * @property decimal $sampling_elevation_end
 * @property decimal $sampling_elevation_accuracy
 * @property string $original_elevation_data
 * @property decimal $sampling_depth_start
 * @property decimal $sampling_depth_end
 * @property decimal $sampling_depth_accuracy
 * @property string $original_depth_data
 * @property string $collecting_date_begin
 * @property string $collecting_date_begin_mask
 * @property string $collecting_date_end
 * @property string $collecting_date_end_mask
 * @property string $collecting_time_begin
 * @property string $collecting_time_end
 * @property string $sampling_method
 * @property string $sampling_fixation
 * @property Imports $Import
 * @property Doctrine_Collection $StagingGtuTagGroups
 * 
 * @method integer             getId()                             Returns the current record's "id" value
 * @method integer             getImportRef()                      Returns the current record's "import_ref" value
 * @method string              getStatus()                         Returns the current record's "status" value
 * @method boolean             getDateIncluded()                   Returns the current record's "date_included" value
 * @method boolean             getTagsMerged()                     Returns the current record's "tags_merged" value
 * @method boolean             getSensitiveInformationWithheld()   Returns the current record's "sensitive_information_withheld" value
 * @method integer             getGtuRef()                         Returns the current record's "gtu_ref" value
 * @method string              getStationType()                    Returns the current record's "station_type" value
 * @method string              getSamplingCode()                   Returns the current record's "sampling_code" value
 * @method string              getSamplingFieldNumber()            Returns the current record's "sampling_field_number" value
 * @method string              getEventClusterCode()               Returns the current record's "event_cluster_code" value
 * @method string              getEventOrder()                     Returns the current record's "event_order" value
 * @method string              getIgNum()                          Returns the current record's "ig_num" value
 * @method string              getIgNumIndexed()                   Returns the current record's "ig_num_indexed" value
 * @method string              getCollections()                    Returns the current record's "collections" value
 * @method string              getCollectors()                     Returns the current record's "collectors" value
 * @method string              getExpeditions()                    Returns the current record's "expeditions" value
 * @method string              getCollectionRefs()                 Returns the current record's "collection_refs" value
 * @method string              getCollectorRefs()                  Returns the current record's "collector_refs" value
 * @method string              getExpeditionRefs()                 Returns the current record's "expedition_refs" value
 * @method string              getIso3166()                        Returns the current record's "iso3166" value
 * @method string              getIso3166Subdivision()             Returns the current record's "iso3166_subdivision" value
 * @method string              getCountries()                      Returns the current record's "countries" value
 * @method string              getTags()                           Returns the current record's "tags" value
 * @method string              getTagsIndexed()                    Returns the current record's "tags_indexed" value
 * @method string              getLocalityText()                   Returns the current record's "locality_text" value
 * @method string              getLocalityTextIndexed()            Returns the current record's "locality_text_indexed" value
 * @method string              getEcologyText()                    Returns the current record's "ecology_text" value
 * @method string              getEcologyTextIndexed()             Returns the current record's "ecology_text_indexed" value
 * @method string              getCoordinatesFormat()              Returns the current record's "coordinates_format" value
 * @method string              getLatitude1()                      Returns the current record's "latitude1" value
 * @method string              getLongitude1()                     Returns the current record's "longitude1" value
 * @method string              getLatitude2()                      Returns the current record's "latitude2" value
 * @method string              getLongitude2()                     Returns the current record's "longitude2" value
 * @method string              getGisType()                        Returns the current record's "gis_type" value
 * @method string              getCoordinatesWkt()                 Returns the current record's "coordinates_wkt" value
 * @method string              getCoordinatesDatum()               Returns the current record's "coordinates_datum" value
 * @method integer             getCoordinatesProjRef()             Returns the current record's "coordinates_proj_ref" value
 * @method string              getCoordinatesOriginal()            Returns the current record's "coordinates_original" value
 * @method decimal             getCoordinatesAccuracy()            Returns the current record's "coordinates_accuracy" value
 * @method string              getCoordinatesAccuracyText()        Returns the current record's "coordinates_accuracy_text" value
 * @method decimal             getStationBaselineElevation()       Returns the current record's "station_baseline_elevation" value
 * @method decimal             getStationBaselineAccuracy()        Returns the current record's "station_baseline_accuracy" value
 * @method decimal             getSamplingElevationStart()         Returns the current record's "sampling_elevation_start" value
 * @method decimal             getSamplingElevationEnd()           Returns the current record's "sampling_elevation_end" value
 * @method decimal             getSamplingElevationAccuracy()      Returns the current record's "sampling_elevation_accuracy" value
 * @method string              getOriginalElevationData()          Returns the current record's "original_elevation_data" value
 * @method decimal             getSamplingDepthStart()             Returns the current record's "sampling_depth_start" value
 * @method decimal             getSamplingDepthEnd()               Returns the current record's "sampling_depth_end" value
 * @method decimal             getSamplingDepthAccuracy()          Returns the current record's "sampling_depth_accuracy" value
 * @method string              getOriginalDepthData()              Returns the current record's "original_depth_data" value
 * @method string              getCollectingDateBegin()            Returns the current record's "collecting_date_begin" value
 * @method string              getCollectingDateBeginMask()        Returns the current record's "collecting_date_begin_mask" value
 * @method string              getCollectingDateEnd()              Returns the current record's "collecting_date_end" value
 * @method string              getCollectingDateEndMask()          Returns the current record's "collecting_date_end_mask" value
 * @method string              getCollectingTimeBegin()            Returns the current record's "collecting_time_begin" value
 * @method string              getCollectingTimeEnd()              Returns the current record's "collecting_time_end" value
 * @method string              getSamplingMethod()                 Returns the current record's "sampling_method" value
 * @method string              getSamplingFixation()               Returns the current record's "sampling_fixation" value
 * @method Imports             getImport()                         Returns the current record's "Import" value
 * @method Doctrine_Collection getStagingGtuTagGroups()            Returns the current record's "StagingGtuTagGroups" collection
 * @method StagingGtu          setId()                             Sets the current record's "id" value
 * @method StagingGtu          setImportRef()                      Sets the current record's "import_ref" value
 * @method StagingGtu          setStatus()                         Sets the current record's "status" value
 * @method StagingGtu          setDateIncluded()                   Sets the current record's "date_included" value
 * @method StagingGtu          setTagsMerged()                     Sets the current record's "tags_merged" value
 * @method StagingGtu          setSensitiveInformationWithheld()   Sets the current record's "sensitive_information_withheld" value
 * @method StagingGtu          setGtuRef()                         Sets the current record's "gtu_ref" value
 * @method StagingGtu          setStationType()                    Sets the current record's "station_type" value
 * @method StagingGtu          setSamplingCode()                   Sets the current record's "sampling_code" value
 * @method StagingGtu          setSamplingFieldNumber()            Sets the current record's "sampling_field_number" value
 * @method StagingGtu          setEventClusterCode()               Sets the current record's "event_cluster_code" value
 * @method StagingGtu          setEventOrder()                     Sets the current record's "event_order" value
 * @method StagingGtu          setIgNum()                          Sets the current record's "ig_num" value
 * @method StagingGtu          setIgNumIndexed()                   Sets the current record's "ig_num_indexed" value
 * @method StagingGtu          setCollections()                    Sets the current record's "collections" value
 * @method StagingGtu          setCollectors()                     Sets the current record's "collectors" value
 * @method StagingGtu          setExpeditions()                    Sets the current record's "expeditions" value
 * @method StagingGtu          setCollectionRefs()                 Sets the current record's "collection_refs" value
 * @method StagingGtu          setCollectorRefs()                  Sets the current record's "collector_refs" value
 * @method StagingGtu          setExpeditionRefs()                 Sets the current record's "expedition_refs" value
 * @method StagingGtu          setIso3166()                        Sets the current record's "iso3166" value
 * @method StagingGtu          setIso3166Subdivision()             Sets the current record's "iso3166_subdivision" value
 * @method StagingGtu          setCountries()                      Sets the current record's "countries" value
 * @method StagingGtu          setTags()                           Sets the current record's "tags" value
 * @method StagingGtu          setTagsIndexed()                    Sets the current record's "tags_indexed" value
 * @method StagingGtu          setLocalityText()                   Sets the current record's "locality_text" value
 * @method StagingGtu          setLocalityTextIndexed()            Sets the current record's "locality_text_indexed" value
 * @method StagingGtu          setEcologyText()                    Sets the current record's "ecology_text" value
 * @method StagingGtu          setEcologyTextIndexed()             Sets the current record's "ecology_text_indexed" value
 * @method StagingGtu          setCoordinatesFormat()              Sets the current record's "coordinates_format" value
 * @method StagingGtu          setLatitude1()                      Sets the current record's "latitude1" value
 * @method StagingGtu          setLongitude1()                     Sets the current record's "longitude1" value
 * @method StagingGtu          setLatitude2()                      Sets the current record's "latitude2" value
 * @method StagingGtu          setLongitude2()                     Sets the current record's "longitude2" value
 * @method StagingGtu          setGisType()                        Sets the current record's "gis_type" value
 * @method StagingGtu          setCoordinatesWkt()                 Sets the current record's "coordinates_wkt" value
 * @method StagingGtu          setCoordinatesDatum()               Sets the current record's "coordinates_datum" value
 * @method StagingGtu          setCoordinatesProjRef()             Sets the current record's "coordinates_proj_ref" value
 * @method StagingGtu          setCoordinatesOriginal()            Sets the current record's "coordinates_original" value
 * @method StagingGtu          setCoordinatesAccuracy()            Sets the current record's "coordinates_accuracy" value
 * @method StagingGtu          setCoordinatesAccuracyText()        Sets the current record's "coordinates_accuracy_text" value
 * @method StagingGtu          setStationBaselineElevation()       Sets the current record's "station_baseline_elevation" value
 * @method StagingGtu          setStationBaselineAccuracy()        Sets the current record's "station_baseline_accuracy" value
 * @method StagingGtu          setSamplingElevationStart()         Sets the current record's "sampling_elevation_start" value
 * @method StagingGtu          setSamplingElevationEnd()           Sets the current record's "sampling_elevation_end" value
 * @method StagingGtu          setSamplingElevationAccuracy()      Sets the current record's "sampling_elevation_accuracy" value
 * @method StagingGtu          setOriginalElevationData()          Sets the current record's "original_elevation_data" value
 * @method StagingGtu          setSamplingDepthStart()             Sets the current record's "sampling_depth_start" value
 * @method StagingGtu          setSamplingDepthEnd()               Sets the current record's "sampling_depth_end" value
 * @method StagingGtu          setSamplingDepthAccuracy()          Sets the current record's "sampling_depth_accuracy" value
 * @method StagingGtu          setOriginalDepthData()              Sets the current record's "original_depth_data" value
 * @method StagingGtu          setCollectingDateBegin()            Sets the current record's "collecting_date_begin" value
 * @method StagingGtu          setCollectingDateBeginMask()        Sets the current record's "collecting_date_begin_mask" value
 * @method StagingGtu          setCollectingDateEnd()              Sets the current record's "collecting_date_end" value
 * @method StagingGtu          setCollectingDateEndMask()          Sets the current record's "collecting_date_end_mask" value
 * @method StagingGtu          setCollectingTimeBegin()            Sets the current record's "collecting_time_begin" value
 * @method StagingGtu          setCollectingTimeEnd()              Sets the current record's "collecting_time_end" value
 * @method StagingGtu          setSamplingMethod()                 Sets the current record's "sampling_method" value
 * @method StagingGtu          setSamplingFixation()               Sets the current record's "sampling_fixation" value
 * @method StagingGtu          setImport()                         Sets the current record's "Import" value
 * @method StagingGtu          setStagingGtuTagGroups()            Sets the current record's "StagingGtuTagGroups" collection
 * 
 * @package    darwin
 * @subpackage model
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseStagingGtu extends DarwinModel
{
    public function setTableDefinition()
    {
        $this->setTableName('staging_gtu');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('import_ref', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('status', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('date_included', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('tags_merged', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('sensitive_information_withheld', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('gtu_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('station_type', 'string', null, array(
             'type' => 'string',
             'notnull' => false,
             'default' => 'station',
             ));
        $this->hasColumn('sampling_code', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('sampling_field_number', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('event_cluster_code', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('event_order', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('ig_num', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('ig_num_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collections', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collectors', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('expeditions', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collection_refs', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collector_refs', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('expedition_refs', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('iso3166', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('iso3166_subdivision', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('countries', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('tags', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('tags_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('locality_text', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('locality_text_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('ecology_text', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('ecology_text_indexed', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('coordinates_format', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('latitude1', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('longitude1', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('latitude2', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('longitude2', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('gis_type', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('coordinates_wkt', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('coordinates_datum', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('coordinates_proj_ref', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('coordinates_original', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('coordinates_accuracy', 'decimal', 15, array(
             'type' => 'decimal',
             'length' => 15,
             'scale' => 4,
             ));
        $this->hasColumn('coordinates_accuracy_text', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('station_baseline_elevation', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('station_baseline_accuracy', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('sampling_elevation_start', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('sampling_elevation_end', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('sampling_elevation_accuracy', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('original_elevation_data', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('sampling_depth_start', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('sampling_depth_end', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('sampling_depth_accuracy', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 4,
             ));
        $this->hasColumn('original_depth_data', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collecting_date_begin', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collecting_date_begin_mask', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collecting_date_end', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collecting_date_end_mask', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collecting_time_begin', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('collecting_time_end', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('sampling_method', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('sampling_fixation', 'string', null, array(
             'type' => 'string',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Imports as Import', array(
             'local' => 'import_ref',
             'foreign' => 'id'));

        $this->hasMany('StagingGtuTagGroups', array(
             'local' => 'id',
             'foreign' => 'staging_gtu_ref'));
    }
}