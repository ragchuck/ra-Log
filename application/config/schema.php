<?php
defined('SYSPATH') or die('No direct access allowed.');

if ( true or ! $channels = Cache::instance()->get('schema.channels'))
{
      $channels = array();
      foreach (ORM::factory('channel')->find_all() as $channel)
      {
            $measure = $channel->measure;
            $channels[] = array(
                  'val' => $channel->key,
                  'label' => $channel->key . ' - ' . $measure->name,
                  'popover' => array(
                        'title' => $measure->name,
                        'content' => $measure->description . '<br>'
                              . '<small>' . str_replace(chr(10),'<br>',$measure->description_add) . '</small>'
                  )
            );
            //Fire::fb($measure->description_add);
      }
      Cache::instance()->set('schema.channels', $channels);
}


return array(
      'import' => array(
            'path' => array(
                  'type' => 'Text',
                  'title' => __('Import-Path'),
                  'help' => __('Path to the import files (SMA WebBox FTP-Target)')
            ),
            'bad_path' => array(
                  'type' => 'Text',
                  'title' => __('Bad-Path'),
                  'help' => __('Path where the failed import-files are copied')
            ),
            'archive' => array(
                  'type' => 'Text',
                  'title' => __('Archive-Path'),
                  'help' => __('Path where the import files are archived, no archiving when empty')
            ),
            'workspace' => array(
                  'type' => 'Text',
                  'title' => __('Workspace-Path'),
                  'help' => __('Path where the work is done, uses temp path of the system')
            ),
            'max_files' => array(
                  'type' => 'Number',
                  'title' => __('Maximum number of files'),
                  'help' => __('Number of files to be loaded at once, 0 = no limit')
            ),
            'ch_filter_type' => array(
                  'type' => 'Select',
                  'title' => __('Channel Filter Typ'),
                  'options' => array(
                        array('val' => 'none', 'label' => __('no filter')),
                        array('val' => 'black', 'label' => __('black')),
                        array('val' => 'whtie', 'label' => __('white')),
                  ),
                  'help' => __('Filter Type:<br>WHITE = only the given list channels will be loaded<br>BLACK = all channels that are not in the list')
            ),
            'ch_filter' => array(
                  'type' => 'Checkboxes',
                  'template' => 'field',
                  'title' => __('Channel Filter'),
                  'options' => $channels,
                  //'fieldClass' => 'well',
                  'help' => __('List of channnels to be loaded or not to be loaded. (depending on filter type)')
            ),
            'load_logs' => array(
                  'type' => 'Checkbox',
                  'title' => __('Load Logs'),
                  'help' => __('Enable loading of WebBox logs')
            ),
            'overwrite' => array(
                  'type' => 'Checkbox',
                  'title' => __('Overwrite'),
                  'help' => __('Enable data overwrite')
            ),
            'test_load' => array(
                  'type' => 'Checkbox',
                  'title' => __('Test-Load'),
                  'help' => __('Enable test-load (Data will be written, but sourcefiles won\'t be deleted)')
            ),
      )
);