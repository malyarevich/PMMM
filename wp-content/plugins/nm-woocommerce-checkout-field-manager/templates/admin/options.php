<?php

$meatGeneral = array('allowed-types'	=> array(	'label'		=> __('Allowed file types', $this->plugin_meta['shortname']),
																								'desc'		=> __('', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_files_allowed',
																								'type'			=> 'text',
																								'default'		=> '*.jpg;*.png',
																								'help'			=> __('Enter the files types e.g: <strong>*.jpg;*.png</strong>', $this->plugin_meta['shortname'])
																								),
					'size-limit'		=>  array(	'label'		=> __('Size limit', $this->plugin_meta['shortname']),
																								'desc'		=> __('Enter size limit in Bytes, leave blank for server default', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_size_limit',
																								'type'			=> 'text',
																								'default'		=> '',
																								'help'			=> __('102400', $this->plugin_meta['shortname'])
																								),
					
					'page-limit'		=>  array(	'label'		=> __('Page limit', $this->plugin_meta['shortname']),
							'desc'		=> __('Enter File Limits to be shown per page', $this->plugin_meta['shortname']),
							'id'			=> $this->plugin_meta['shortname'].'_page_limit',
							'type'			=> 'text',
							'default'		=> '',
							'help'			=> __('e.g: 30', $this->plugin_meta['shortname'])
					),
					'notify-email'		=>  array(	'label'		=> __('Notify by Email', $this->plugin_meta['shortname']),
																								'desc'		=> __('Send email to admin on each file uploaded/directory created by user', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_notify_email',
																								'type'			=> 'radio',
																								'help'			=> __('', $this->plugin_meta['shortname']),
																								'options'		=> array('yes'	=> __('Yes', $this->plugin_meta['shortname']),
																										'no'	=> __('No', $this->plugin_meta['shortname'])
																													)
																								),
					'allow-multi'		=>  array(	'label'		=> __('Allow Multi File Uploads', $this->plugin_meta['shortname']),
							'desc'		=> __('Allow users to upload Multiple Files at a time', $this->plugin_meta['shortname']),
							'id'			=> $this->plugin_meta['shortname'].'_allow_multi',
							'type'			=> 'radio',
							'help'			=> __('', $this->plugin_meta['shortname']),
							'options'		=> array('yes'	=> __('Yes', $this->plugin_meta['shortname']),
									'no'	=> __('No', $this->plugin_meta['shortname'])
							)
					),
		
					'file-attachment'		=>  array(	'label'		=> __('Send file as attachment', $this->plugin_meta['shortname']),
																								'desc'		=> __('Send upload file as attachment to admin', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_file_attachment',
																								'type'			=> 'radio',
																								'help'			=> __('', $this->plugin_meta['shortname']),
																								'options'		=> array('yes'	=> __('Yes', $this->plugin_meta['shortname']),
																										'no'	=> __('No', $this->plugin_meta['shortname'])
																													)
																								),
					'file-meta'		=>  array(	'label'		=> __('Use additional input as file meta', $this->plugin_meta['shortname']),
																								'desc'		=> __('Use additional fields with files meta', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_use_filemeta',
																								'type'			=> 'radio',
																								'help'			=> __('', $this->plugin_meta['shortname']),
																								'options'		=> array('yes'	=> __('Yes', $this->plugin_meta['shortname']),
																										'no'	=> __('No', $this->plugin_meta['shortname'])
																													)
																								),
					'secure-link'		=>  array(	'label'		=> __('Secuer download link', $this->plugin_meta['shortname']),
							'desc'		=> __('Enable/Disable secure download link', $this->plugin_meta['shortname']),
							'id'			=> $this->plugin_meta['shortname'].'_secure_link',
							'type'			=> 'radio',
							'help'			=> __('', $this->plugin_meta['shortname']),
							'options'		=> array('yes'	=> __('Enable', $this->plugin_meta['shortname']),
									'no'	=> __('Disable', $this->plugin_meta['shortname'])
							)
					),		
					'link-expires'		=>  array(	'label'		=> __('Download Link Expires', $this->plugin_meta['shortname']),
																								'desc'		=> __('Enter expire time limit for download in minutes', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_link_expires',
																								'type'			=> 'text',
																								'default'		=> '5',
																								'help'			=> __('e.g: 5', $this->plugin_meta['shortname'])
																								),
					'show-share'		=>  array(	'label'		=> __('Show shared files', $this->plugin_meta['shortname']),
																								'desc'		=> __('Show shared files to users in fron-end', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_show_share',
																								'type'			=> 'radio',
																								'help'			=> __('', $this->plugin_meta['shortname']),
																								'options'		=> array('yes'	=> __('Yes', $this->plugin_meta['shortname']),
																										'no'	=> __('No', $this->plugin_meta['shortname'])
																													)
																								),
					'share-with'		=>  array(	'label'		=> __('Shared Roles', $this->plugin_meta['shortname']),
																								'desc'		=> __('Enter roles separated by comma to whom files should be shared', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_share_with',
																								'type'			=> 'text',
																								'default'		=> 'all',
																								'help'			=> __('leave blank for all or type: editor,subscriber,auther', $this->plugin_meta['shortname'])
																								),);
					

$meatDialog = array('file-uploaded'	=> array(	'label'		=> __('File saved message', $this->plugin_meta['shortname']),
		'desc'		=> __('This message will be shown when file is saved', $this->plugin_meta['shortname']),
		'id'			=> $this->plugin_meta['shortname'].'_file_saved',
		'type'			=> 'textarea',
		'default'		=> '',
		'help'			=> ''),
		
		'dir-created'	=> array(	'label'		=> __('Directory created message', $this->plugin_meta['shortname']),
		'desc'		=> __('This message will be shown when directory is created', $this->plugin_meta['shortname']),
		'id'			=> $this->plugin_meta['shortname'].'_dir_created',
		'type'			=> 'textarea',
		'default'		=> '',
		'help'			=> ''),);

$proFeatures = '<ol>';
$proFeatures .= '<li>'.__('Receive uploaded file(s) in email as attachment', $this->plugin_meta['shortname']).'</li>';
$proFeatures .= '<li>'.__('Download all files and directories as zip for each user', $this->plugin_meta['shortname']).'</li>';
$proFeatures .= '<li>'.__('Attach umlimited additional input fields with file', $this->plugin_meta['shortname']).'</li>';
$proFeatures .= '<li>'.__('Search files and directory option', $this->plugin_meta['shortname']).'</li>';
$proFeatures .= '<li>'.__('Secure files from unauthorised download', $this->plugin_meta['shortname']).'</li>';
$proFeatures .= '<li>'.__('Allow multiple file upload', $this->plugin_meta['shortname']).'</li>';
$proFeatures .= '</ol>';

$proFeatures .= '<br><br>Purchase URL: <a href="http://www.najeebmedia.com/n-media-file-repository-plugin-for-wordpress/">Here</a>';
$proFeatures .= '<br>More information contact: <a href="mailto:sales@najeebmedia.com">sales@najeebmedia.com</a>';


$meatPro = array('pro-feature'	=> array(	'desc'		=> $proFeatures,
		'type'		=> 'file',
		'id'		=> 'contact-us.php',
),);

$this -> the_options = array('general-settings'	=> array(	'name'		=> __('Basic Setting', $this->plugin_meta['shortname']),
														'type'	=> 'tab',
														'desc'	=> __('Set options as per your need', $this->plugin_meta['shortname']),
														'meat'	=> $meatGeneral,
														
													),
						'email-template'	=> array(	'name'		=> __('Dialog Messages', $this->plugin_meta['shortname']),
								'type'	=> 'tab',
								'desc'	=> __('Set message as per your need', $this->plugin_meta['shortname']),
								'meat'	=> $meatDialog,
								
						),
		
						'pro-features'	=> array(	'name'		=> __('Need more?', $this->plugin_meta['shortname']),
								'type'	=> 'tab',
								'desc'	=> __('Contact us if you need more on top of this plugin', $this->plugin_meta['shortname']),
								'meat'	=> $meatPro,
						
						),
					);

//print_r($repo_options);