<style>
table, th, td {
   border: 1px solid black;
}

th, td {
    padding: 15px;
    text-align: left;
}
</style>
<div class="page">
<div>

<table >
 <tr>   
        <th>id</th>
        <th>id db</th>       
        <th>gtu_ref</th>
        <th>station_type</th>
        <th>sampling_code</th>
        <th>sampling_field_number</th>
        <th>event_cluster_code</th>
        <th>event_order</th>
        <th>ig_num</th>
        <th>ig_num_indexed</th>
        <th>collections</th>
        <th>collectors</th>
        <th>expeditions</th>
        <th>collection_refs</th>
        <th>collector_refs</th>
        <th>expedition_refs</th>
        <th>iso3166</th>
        <th>iso3166_subdivision</th>
        <th>countries</th>
        <th>tags</th>
        <th>tags_indexed</th>
        <th>locality_text</th>
        <th>locality_text_indexed</th>
        <th>ecology_text</th>
        <th>ecology_text_indexed</th>
        <th>coordinates_format</th>
        <th>latitude1</th>
        <th>longitude1</th>
        <th>latitude2</th>
        <th>longitude2</th>
        <th>gis_type</th>
        <th>coordinates_wkt</th>
        <th>coordinates_datum</th>
        <th>coordinates_proj_ref</th>
        <th>coordinates_original</th>
        <th>coordinates_accuracy</th>
        <th>coordinates_accuracy_text</th>
        <th>station_baseline_elevation</th>
        <th>station_baseline_accuracy</th>
        <th>sampling_elevation_start</th>
        <th>sampling_elevation_end</th>
        <th>sampling_elevation_accuracy</th>
        <th>original_elevation_data</th>
        <th>sampling_depth_start</th>
        <th>sampling_depth_end</th>
        <th>sampling_depth_accuracy</th>
        <th>original_depth_data</th>
        <th>collecting_date_begin</th>
        <th>collecting_date_begin_mask</th>
        <th>collecting_date_end</th>
        <th>collecting_date_end_mask</th>
        <th>collecting_time_begin</th>
        <th>collecting_time_end</th>
        <th>sampling_method</th>
        <th>sampling_fixation</th>
        <th>the_geom</th>
        <th>imported</th>
        
      </tr>  

 <?php $i=0; foreach($items as $item):?>
    <tr>
        <td><?php print(++$i);?></td>
        <td><?php print($item['id']);?></td>        
        <td><?php print($item['gtu_ref']);?></td>
        <td><?php print($item['station_type']);?></td>
        <td><?php print($item['sampling_code']);?></td>
        <td><?php print($item['sampling_field_number']);?></td>
        <td><?php print($item['event_cluster_code']);?></td>
        <td><?php print($item['event_order']);?></td>
        <td><?php print($item['ig_num']);?></td>
        <td><?php print($item['ig_num_indexed']);?></td>
        <td><?php print($item['collections']);?></td>
        <td><?php print($item['collectors']);?></td>
        <td><?php print($item['expeditions']);?></td>
        <td><?php print($item['collection_refs']);?></td>
        <td><?php print($item['collector_refs']);?></td>
        <td><?php print($item['expedition_refs']);?></td>
        <td><?php print($item['iso3166']);?></td>
        <td><?php print($item['iso3166_subdivision']);?></td>
        <td><?php print($item['countries']);?></td>
        <td><?php print($item['tags']);?></td>
        <td><?php print($item['tags_indexed']);?></td>
        <td><?php print($item['locality_text']);?></td>
        <td><?php print($item['locality_text_indexed']);?></td>
        <td><?php print($item['ecology_text']);?></td>
        <td><?php print($item['ecology_text_indexed']);?></td>
        <td><?php print($item['coordinates_format']);?></td>
        <td><?php print($item['latitude1']);?></td>
        <td><?php print($item['longitude1']);?></td>
        <td><?php print($item['latitude2']);?></td>
        <td><?php print($item['longitude2']);?></td>
        <td><?php print($item['gis_type']);?></td>
        <td><?php print($item['coordinates_wkt']);?></td>
        <td><?php print($item['coordinates_datum']);?></td>
        <td><?php print($item['coordinates_proj_ref']);?></td>
        <td><?php print($item['coordinates_original']);?></td>
        <td><?php print($item['coordinates_accuracy']);?></td>
        <td><?php print($item['coordinates_accuracy_text']);?></td>
        <td><?php print($item['station_baseline_elevation']);?></td>
        <td><?php print($item['station_baseline_accuracy']);?></td>
        <td><?php print($item['sampling_elevation_start']);?></td>
        <td><?php print($item['sampling_elevation_end']);?></td>
        <td><?php print($item['sampling_elevation_accuracy']);?></td>
        <td><?php print($item['original_elevation_data']);?></td>
        <td><?php print($item['sampling_depth_start']);?></td>
        <td><?php print($item['sampling_depth_end']);?></td>
        <td><?php print($item['sampling_depth_accuracy']);?></td>
        <td><?php print($item['original_depth_data']);?></td>
        <td><?php print($item['collecting_date_begin']);?></td>
        <td><?php print($item['collecting_date_begin_mask']);?></td>
        <td><?php print($item['collecting_date_end']);?></td>
        <td><?php print($item['collecting_date_end_mask']);?></td>
        <td><?php print($item['collecting_time_begin']);?></td>
        <td><?php print($item['collecting_time_end']);?></td>
        <td><?php print($item['sampling_method']);?></td>
        <td><?php print($item['sampling_fixation']);?></td>
        <td><?php print($item['the_geom']);?></td>
        <td><?php print($item['imported']);?></td>
        
      </tr>  
 <?php endforeach;?>
</table>
</div>
</div>