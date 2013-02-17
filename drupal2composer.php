#!/usr/bin/env php

<?php

$drupal_version = '7.x';
$modules   = array(
    'features',
    'cck',
    'wysiwyg',
    'less',
    'l10n_update',
    'i18n',
    'variable',
    'i18n_menu_node',
    'imce',
    'imce_wysiwyg',
    'imce_mkdir',
    'google_analytics',
    );

$data['name']              = 'drupal';
$data['description']       = 'Main repository for ... .';
$data['license']           = 'MIT';
$data['minimum-stability'] = 'dev';
$data['authors'][]           = array(
    'name'  => 'Firstname Lastname',
    'email' => 'example@example.com',
    'role'  => 'maintainer',
    );
$data['require']['php']    = '>=5.3';
$data['require-dev']       = array(
    'lapistano/proxy-object' => 'dev-master',
    );

foreach ($modules as $module) {
    $url = 'http://updates.drupal.org/release-history/' . $module . '/' . $drupal_version;
    $xml = new SimpleXMLElement(file_get_contents($url));

    foreach ($xml->releases->release as $release) {
        if ((string) $release->version_major ===  (string) $xml->recommended_major || (string) $release->version_major === (string) $xml->default_major) {
            $name    = 'drupal/' . $module;
            $version = (string) $release->version_major . '.' . (string) $release->version_patch;
            $url     = (string) $release->files->file[0]->url;
            $hash    = (string) $release->files->file[0]->md5;
            $type    = explode('.', (string) $release->files->file[0]->archive_type);
            
            $data['repositories'][] =
                array('type'    => 'package',
                      'package' => array(
                          'name'    => $name,
                          'version' => $version,
                          'type'    => 'drupal-module',
                          'dist' => array(
                              'url'  => $url,
                              'hash' => $hash,
                              'type' => $type[0],
                              ),
                          'require'  => array(
                              'composer/installers' => 'v1.0.0',
                              )
                          ),
                    );
            $data['require'][$name] = $version;
            $data['extra']['installer-paths']['sites/all/modules/{$name}/'][] = $name;
            
            break;
        }
    }
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

?>
