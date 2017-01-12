<?php

//icontact
function snp_ml_get_ic_lists($ml_ic_username='', $ml_ic_addid='', $ml_ic_apppass='')
{
	require_once SNP_DIR_PATH . '/include/icontact/iContactApi.php';
	$list = array();
	if (
			(snp_get_option('ml_ic_username') && snp_get_option('ml_ic_addid') && snp_get_option('ml_ic_apppass')) ||
			($ml_ic_username && $ml_ic_addid && $ml_ic_apppass)
	)
	{
		if (!$ml_ic_username || !$ml_ic_addid || !$ml_ic_apppass)
		{
			$ml_ic_username = snp_get_option('ml_ic_username');
			$ml_ic_addid = snp_get_option('ml_ic_addid');
			$ml_ic_apppass = snp_get_option('ml_ic_apppass');
		}
		iContactApi::getInstance()->setConfig(array(
			'appId' => $ml_ic_addid,
			'apiPassword' => $ml_ic_apppass,
			'apiUsername' => $ml_ic_username
		));
		$oiContact = iContactApi::getInstance();
		try
		{
			$res = $oiContact->getLists();
			foreach ((array) $res as $v)
			{
				$list[$v->listId] = array('name' => $v->name);
			}
			//var_dump($oiContact->getLists());
		}
		catch (Exception $oException)
		{
			// Error
			// Catch any exceptions
			// Dump errors
			//var_dump($oiContact->getErrors());
			// Grab the last raw request data
			//var_dump($oiContact->getLastRequest());
			// Grab the last raw response data
			//var_dump($oiContact->getLastResponse());
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_aw_remove_auth()
{
	$return = array();
	delete_option('snp_ml_aw_auth_info');
	$return['Ok'] = true;
	return $return;
}
function snp_ml_get_aw_auth($ml_aw_auth_code)
{
	$return = array();
	require_once SNP_DIR_PATH . '/include/aweber/aweber_api.php';
	$descr = '';
	try
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID($ml_aw_auth_code);
	}
	catch (AWeberAPIException $exc)
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
		if(isset($exc->message))
		{
			$descr = $exc->message;
			$descr = preg_replace('/http.*$/i', '', $descr);	 # strip labs.aweber.com documentation url from error message
			$descr = preg_replace('/[\.\!:]+.*$/i', '', $descr); # strip anything following a . : or ! character
			$descr = '('.$descr.')';
		}
	}
	catch (AWeberOAuthDataMissing $exc)
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
	}
	catch (AWeberException $exc)
	{
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
	}
	if (!$access_secret) 
	{
		$return['Error'] = 'Unable to connect to your AWeber Account ' .$descr;
		$return['Ok'] = false;
	}
	else
	{
		$ml_aw_auth_info = array(
			'consumer_key' => $consumer_key,
			'consumer_secret' => $consumer_secret,
			'access_key' => $access_key,
			'access_secret' => $access_secret,
		);
		update_option('snp_ml_aw_auth_info',$ml_aw_auth_info);
		$return['Ok'] = true;
	}
	return $return;
}
// aweber
function snp_ml_get_aw_lists()
{
	require_once SNP_DIR_PATH . '/include/aweber/aweber_api.php';
	$list = array();
	if (get_option('snp_ml_aw_auth_info'))
	{
		$aw = get_option('snp_ml_aw_auth_info');
		try {
			$aweber = new AWeberAPI($aw['consumer_key'], $aw['consumer_secret']);
			$account = $aweber->getAccount($aw['access_key'], $aw['access_secret']);
			$res = $account->lists;
			if($res)
			{
				foreach ((array) $res->data['entries'] as $v)
				{
					$list[$v['id']] = array('name' => $v['name']);
				}
			}
		}
		catch (AWeberException $e) 
		{
		    //echo $e;
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// mailchimp
function snp_ml_get_mc_lists($ml_mc_apikey='')
{
	require_once SNP_DIR_PATH . '/include/mailchimp/MC_Lists.php';
	$list = array();
	if (snp_get_option('ml_mc_apikey') || $ml_mc_apikey)
	{
		try
	    {
			if ($ml_mc_apikey)
			{
				$rest = new MC_Lists($ml_mc_apikey);
			}
			else
			{
				$rest = new MC_Lists(snp_get_option('ml_mc_apikey'));
			}
			$retval = json_decode($rest->getAll(array('fields' => 'lists.id,lists.name', 'count' => '100')));
			if (is_object($retval))
			{
				foreach ($retval->lists as $v)
				{
					$list[$v -> id] = array('name' => $v -> name);
				}
			}
		}
	    catch (Exception $exc)
	    {

	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_sendgrid_lists($ml_sendgrid_username='', $ml_sendgrid_password ='')
{
	require_once SNP_DIR_PATH . '/include/sendgrid/sendgrid_api.php';
	$list = array();
	if (snp_get_option('ml_sendgrid_username') || $ml_sendgrid_username)
	{
		try
	    {
			if ($ml_sendgrid_username)
			{
				$rest = new snp_sendgrid_class($ml_sendgrid_username, $ml_sendgrid_password);
			}
			else
			{
				$rest = new snp_sendgrid_class(snp_get_option('ml_sendgrid_username'), snp_get_option('ml_sendgrid_password'));
			}
			$response = $rest->getLists();
			$response = json_decode($response);
			foreach ((array) $response->lists as $v)
			{
				$list[$v->id] = array('name' => $v->name);
			}
		}
	    catch (Exception $exc)
	    {

	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_sendinblue_lists($ml_sendinblue_apikey='')
{
	require_once SNP_DIR_PATH . '/include/sendinblue/Mailin.php';
	$list = array();
	if (snp_get_option('ml_sendinblue_apikey') || $ml_sendinblue_apikey)
	{
		try
	    {
			if ($ml_sendinblue_apikey)
			{
				$api = new Mailin("https://api.sendinblue.com/v2.0", $ml_sendinblue_apikey);
			}
			else
			{
				$api = new Mailin("https://api.sendinblue.com/v2.0", snp_get_option('ml_sendinblue_apikey'));
			}
			$data = array("page" => 1, "page_limit" => 50);

    		$response = $api->get_lists($data);
			if (isset($response['code'])&&$response['code'] == 'success')
			{
				foreach ((array) $response['data']['lists'] as $v)
				{
					$list[$v['id']] = array('name' => $v['name']);
				}
			}
		}
	    catch (Exception $exc)
	    {

	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_freshmail_lists($ml_freshmail_apikey='', $ml_freshmail_apisecret='')
{
	require_once SNP_DIR_PATH . '/include/freshmail/class.rest.php';
	$list = array();
	if (snp_get_option('ml_freshmail_apikey') || $ml_freshmail_apikey)
	{
            try
	    {
	    	$rest = new FmRestAPI();
			if ($ml_freshmail_apikey)
			{
				$rest->setApiKey( $ml_freshmail_apikey );
				$rest->setApiSecret( $ml_freshmail_apisecret );
			}
			else
			{
				$rest->setApiKey( snp_get_option('ml_freshmail_apikey') );
				$rest->setApiSecret(snp_get_option('ml_freshmail_apisecret') );
			}
			$response = $rest->doRequest('subscribers_list/lists');
			if (!empty($response))
			{
				foreach ((array) $response['lists'] as $v)
				{
					$list[$v['subscriberListHash']] = array('name' => $v['name']);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_sendlane_lists($ml_sendlane_apikey='', $ml_sendlane_hash='', $ml_sendlane_subdomain='')
{
	require_once SNP_DIR_PATH . '/include/sendlane/snp_sendlane.php';
	$list = array();
	if (snp_get_option('ml_sendlane_apikey') || $ml_sendlane_apikey)
	{
            try
	    {
                if ($ml_sendlane_apikey)
                {
                    $rest = new snp_sendlane($ml_sendlane_apikey, $ml_sendlane_hash, $ml_sendlane_subdomain);
                }
                else
                {
                    $rest = new snp_sendlane(snp_get_option('ml_sendlane_apikey'), snp_get_option('ml_sendlane_hash'), snp_get_option('ml_sendlane_subdomain'));
                }
                $response = json_decode($rest->getLists());
                if (!empty($response))
                {
                    foreach ($response as $v)
                    {
                        $list[$v->list_id] = array('name' => $v->list_name);
                    }
                }
            }
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_mailrelay_lists($ml_mailrelay_apikey='', $ml_mailrelay_address='')
{
	require_once SNP_DIR_PATH . '/include/mailrelay/snp_mailrelay.php';
	$list = array();
	if (snp_get_option('ml_mailrelay_apikey') || $ml_mailrelay_apikey)
	{
            try
	    {
			if ($ml_mailrelay_apikey)
			{
                            $rest = new snp_mailrelay($ml_mailrelay_apikey, $ml_mailrelay_address);
			}
			else
			{
                            $rest = new snp_mailrelay(snp_get_option('ml_mailrelay_apikey'), snp_get_option('ml_mailrelay_address'));
			}
			$response = $rest->getLists();
			if (!empty($response))
			{
				foreach ((array) $response->data as $v)
				{
					$list[$v->id] = array('name' => $v->name);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_mailup_lists($ml_mailup_clientid='', $ml_mailup_clientsecret='', $ml_mailup_login='', $ml_mailup_password='')
{
	require_once SNP_DIR_PATH . '/include/mailup/snp_mailup.php';
	$list = array();
	if (snp_get_option('ml_mailup_clientid') || $ml_mailup_clientid)
	{
            try
	    {
                if ($ml_mailup_clientid)
                {
                    $rest = new snp_mailup($ml_mailup_clientid, $ml_mailup_clientsecret, $ml_mailup_login, $ml_mailup_password);
                }
                else
                {
                    $rest = new snp_mailup(snp_get_option('ml_mailup_clientid'), snp_get_option('ml_mailup_clientsecret'), snp_get_option('ml_mailup_login'), snp_get_option('ml_mailup_password'));
                }
                $response = $rest->getLists();
                if (!empty($response))
                {
                        foreach ((array) $response as $v)
                        {
                                $list[$v->idList] = array('name' => trim($v->Name));
                        }
                }
            }
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_ontraport_lists($ml_ontraport_apiid='', $ml_ontraport_apikey='')
{
	require_once SNP_DIR_PATH . '/include/ontraport/snp_ontraport.php';
	$list = array();
	if (snp_get_option('ml_ontraport_apikey') || $ml_ontraport_apikey)
	{
		try
	    {
	    	
			if ($ml_ontraport_apikey)
			{
				$rest = new snp_ontraport($ml_ontraport_apiid, $ml_ontraport_apikey);
			}
			else
			{
				$rest = new snp_ontraport(snp_get_option('ml_ontraport_apiid'), snp_get_option('ml_ontraport_apikey'));
			}
			$response = $rest->getTags();
			if (!empty($response))
			{
				foreach ((array) $response as $v)
				{
					$list[$v] = array('name' => $v);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_sendreach_lists($ml_sendreach_pubkey='', $ml_sendreach_privkey='')
{
	require_once SNP_DIR_PATH . '/include/sendreach/sendreach_api.php';
	$list = array();
	if (snp_get_option('ml_sendreach_pubkey') || $ml_sendreach_pubkey)
	{
		try
	    {
			if ($ml_sendreach_pubkey)
			{
				$rest = new snp_sendreach($ml_sendreach_pubkey, $ml_sendreach_privkey);
			}
			else
			{
				$rest = new snp_sendreach(snp_get_option('ml_sendreach_pubkey'), snp_get_option('ml_sendreach_privkey'));
			}
			$response = $rest->getLists();
			if (!empty($response))
			{
				foreach ((array) $response as $v)
				{
					$list[$v['general']['list_uid']] = array('name' => $v['general']['name']);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_sendpulse_lists($ml_sendpulse_id='', $ml_sendpulse_apisecret='')
{
	require_once SNP_DIR_PATH . '/include/sendpulse/sendpulse.php';
	$list = array();
	if (snp_get_option('ml_sendpulse_id') || $ml_sendpulse_id)
	{
		try
	    {
			if ($ml_sendpulse_id)
			{
				$rest = new snp_sendpulse($ml_sendpulse_id, $ml_sendpulse_apisecret);
			}
			else
			{
				$rest = new snp_sendpulse(snp_get_option('ml_sendpulse_id'), snp_get_option('ml_sendpulse_apisecret'));
			}
			$response = $rest->getLists();
			if (!empty($response))
			{
				foreach ($response as $k=>$v)
				{
					$list[$v['id']] = array('name' => $v['name']);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_mailjet_lists($ml_mailjet_apikey='', $ml_mailjet_apisecret='')
{
	require_once SNP_DIR_PATH . '/include/mailjet/mailjet_class.php';
	$list = array();
	if (snp_get_option('ml_mailjet_apikey') || $ml_mailjet_apikey)
	{
		try
		{
			if ($ml_mailjet_apikey)
			{
				$rest = new snp_mailjet($ml_mailjet_apikey, $ml_mailjet_apisecret);
			}
			else
			{
				$rest = new snp_mailjet(snp_get_option('ml_mailjet_apikey'), snp_get_option('ml_mailjet_apisecret'));
			}
			$response = $rest->getLists();
			if (!empty($response))
			{
				foreach ((array) $response->Data as $v)
				{
					$list[$v->ID] = array('name' => $v->Name);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_elasticemail_lists($ml_elasticemail_apikey='')
{
	require_once SNP_DIR_PATH . '/include/elasticemail/ElasticEmail.php';
	$list = array();
	if (snp_get_option('ml_elasticemail_apikey') || $ml_elasticemail_apikey)
	{
		try
	    {
			if ($ml_elasticemail_apikey)
			{
				$rest= new ElasticEmail($ml_elasticemail_apikey);
			}
			else
			{
				$rest= new ElasticEmail(snp_get_option('ml_elasticemail_apikey'));
			}
			$response = $rest->get_lists();
			if (!empty($response))
			{
				foreach ((array) $response as $v)
				{
					$list[$v] = array('name' => $v);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_benchmarkemail_lists($ml_benchmarkemail_apikey='')
{
	require_once SNP_DIR_PATH . '/include/benchmarkemail/snp_benchmark_class.php';
	$list = array();
	if (snp_get_option('ml_benchmarkemail_apikey') || $ml_benchmarkemail_apikey)
	{
		try
	    {
			if ($ml_benchmarkemail_apikey)
			{
				$rest= new snp_benchmark_class($ml_benchmarkemail_apikey);
			}
			else
			{
				$rest= new snp_benchmark_class(snp_get_option('ml_benchmarkemail_apikey'));
			}
			$response = $rest->getLists();
			if (!empty($response))
			{
				foreach ((array) $response as $v)
				{
					$list[$v['id']] = array('name' => $v['listname']);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_myemma_lists($ml_myemma_account_id='', $ml_myemma_pubkey='', $ml_myemma_privkey='')
{
	require_once SNP_DIR_PATH . '/include/myemma/Emma.php';
	$list = array();
	if (snp_get_option('ml_myemma_account_id') || $ml_myemma_account_id)
	{
		try
	    {

			if ($ml_myemma_account_id)
			{
				$rest = new Emma($ml_myemma_account_id, $ml_myemma_pubkey, $ml_myemma_privkey);
			}
			else
			{
				$rest = new Emma(snp_get_option('ml_myemma_account_id'), snp_get_option('ml_myemma_pubkey'), snp_get_option('ml_myemma_privkey'));
			}
			
			$response = $rest->myGroups();
			$response = json_decode($response);
			if (!empty($response))
			{
				foreach ((array) $response as $v)
				{
					$list[$v->member_group_id] = array('name' => $v->group_name);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_mailerlite_lists($ml_mailerlite_apikey='')
{
	require_once SNP_DIR_PATH . '/include/mailerlite/ML_Lists.php';
	$list = array();
	if (snp_get_option('ml_mailerlite_apikey') || $ml_mailerlite_apikey)
	{
		try
	    {
			if ($ml_mailerlite_apikey)
			{
				$rest = new ML_Lists( $ml_mailerlite_apikey );
			}
			else
			{
				$rest = new ML_Lists( snp_get_option('ml_mailerlite_apikey') );
			}
			$response = json_decode($rest->getAll( ));
			if (!empty($response))
			{
				foreach ($response->Results as $v)
				{
					$list[$v->id] = array('name' => $v->name);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_rocketresponder_lists($ml_rocketresponder_apipublic='', $ml_rocketresponder_apiprivate='')
{
	require_once SNP_DIR_PATH . '/include/rocketresponder/RocketResponder.class.php';
	$list = array();
	if (snp_get_option('ml_rocketresponder_apipublic') || $ml_rocketresponder_apipublic)
	{
		try
	    {
			if ($ml_rocketresponder_apipublic)
			{
				$api = new RocketResponder($ml_rocketresponder_apipublic, $ml_rocketresponder_apiprivate, 1);
			}
			else
			{
				$api = new RocketResponder(snp_get_option('ml_rocketresponder_apipublic'), snp_get_option('ml_rocketresponder_apiprivate'), 1);
			}
			$response = $api->getlists();
			if ($response -> status == 'Success')
			{
				foreach ((array) $response->list as $v)
				{
					$list[$v->LID] = array('name' => $v->Name);
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
function snp_ml_get_activecampaign_lists($ml_activecampaign_apiurl='', $ml_activecampaign_apikey='')
{
	require_once SNP_DIR_PATH . '/include/activecampaign/ActiveCampaign.class.php';			
	$list = array();
	if (snp_get_option('ml_activecampaign_apikey') || $ml_activecampaign_apikey)
	{
		try
	    {
	   
			if ($ml_activecampaign_apikey)
			{
				$ac = new ActiveCampaign($ml_activecampaign_apiurl, $ml_activecampaign_apikey);		
			}
			else
			{
				$ac = new ActiveCampaign(snp_get_option('ml_activecampaign_apiurl'), snp_get_option('ml_activecampaign_apikey'));			
			}
			
			$response = $ac->api("list/list", array('ids' => 'all'));
			if (!empty($response) && $response->success==1)
			{
				foreach ($response as $v)
				{
					if(is_object($v))
					{
						$list[$v->id] = array('name' => $v->name);
					}
				}
			}
		}
	    catch (Exception $exc)
	    {
			$list[0] = array('name' => 'Connection problem');
			return $list;
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}


// campaing monitor
function snp_ml_get_cm_lists($ml_cm_clientid='', $ml_cm_apikey='')
{
	require_once SNP_DIR_PATH . '/include/campaignmonitor/csrest_clients.php';
	$list = array();
	if (
			(snp_get_option('ml_cm_clientid') && snp_get_option('ml_cm_apikey')) ||
			($ml_cm_clientid && $ml_cm_apikey)
	)
	{
		if ($ml_cm_clientid && $ml_cm_apikey)
		{
			$wrap = new CS_REST_Clients($ml_cm_clientid, $ml_cm_apikey);
		}
		else
		{
			$wrap = new CS_REST_Clients(snp_get_option('ml_cm_clientid'), snp_get_option('ml_cm_apikey'));
		}
		$res = $wrap->get_lists();
		if ($res->was_successful())
		{
			foreach ((array) $res->response as $v)
			{
				$list[$v->ListID] = array('name' => $v->Name);
			}
		}
		else
		{
			// Error
			//echo 'Failed with code ' . $res->http_status_code . "\n<br /><pre>";
			//var_dump($res->response);
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// mymail
function snp_ml_get_mm_lists()
{
	$list = array();
	$args = array(
		'orderby'       => 'name', 
		'order'         => 'ASC',
		'hide_empty'    => false, 
		'exclude'       => array(), 
		'exclude_tree'  => array(), 
		'include'       => array(),
		'fields'        => 'all', 
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'pad_counts'    => false, 
		'cache_domain'  => 'core'
	); 
	$list = array();
	if(function_exists('mymail'))
	{
	    // 2.x
	    $lists = mymail('lists')->get();
	    foreach($lists as $v)
	    {
		if($v->ID && $v->name)
		{
		    $list[$v->ID] = array('name' => $v->name);
		}
	    }
	}
	elseif(is_tax( 'newsletter_lists'))							//							tutaj
	{
	    // 1.x
	    $lists=get_terms( 'newsletter_lists', $args );
	    print_r($lists);
	    foreach($lists as $v)
	    {
	    														//	print_r($v);
		if($v->slug && $v->name)
		{
		    $list[$v->slug] = array('name' => $v->name);
		}
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// sendpress
function snp_ml_get_sp_lists()
{
	$list = array();
	if (defined('SENDPRESS_VERSION'))
	{
	    $lists = SendPress_Data::get_lists();
	    foreach($lists->posts as $v)
	    {
		if($v->ID && $v->post_title)
		{
		    $list[$v->ID] = array('name' => $v->post_title);
		}
	    }
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// wysija
function snp_ml_get_wy_lists()
{
	$list = array();
	if(class_exists('WYSIJA'))
	{
		$modelList = WYSIJA::get('list','model');
		$wysijaLists = $modelList->get(array('name','list_id'),array('is_enabled'=>1));
		foreach($wysijaLists as $v)
		{
			$list[$v['list_id']] = array('name' => $v['name']);
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}
// getresponse
function snp_ml_get_gr_lists($ml_gr_apikey='')
{
	require_once SNP_DIR_PATH . '/include/getresponse/jsonRPCClient.php';
	$list = array();
	if (snp_get_option('ml_gr_apikey') || $ml_gr_apikey)
	{
		if (!$ml_gr_apikey)
		{
			$ml_gr_apikey = snp_get_option('ml_gr_apikey');
		}
		$api = new jsonRPCClient('http://api2.getresponse.com');
		try
		{
			$result = $api->get_campaigns($ml_gr_apikey);
			foreach ((array) $result as $k => $v)
			{
				$list[$k] = array('name' => $v['name']);
			}
		}
		catch (Exception $e)
		{
			//die($e->getMessage());
			// Error
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// Constant Contact
function snp_ml_get_cc_lists($ml_cc_username='', $ml_cc_pass='')
{
	require_once SNP_DIR_PATH . '/include/constantcontact/class.cc.php';

	$list = array();
	if (
			(snp_get_option('ml_cc_username') && snp_get_option('ml_cc_pass')) ||
			($ml_cc_username && $ml_cc_pass)
	)
	{
		if ($ml_cc_username && $ml_cc_pass)
		{
			$cc = new cc($ml_cc_username, $ml_cc_pass);
		}
		else
		{
			$cc = new cc(snp_get_option('ml_cc_username'), snp_get_option('ml_cc_pass'));
		}
		$res = $cc->get_all_lists('lists');
		if ($res)
		{
			foreach ((array) $res as $v)
			{
				$list[$v['id']] = array('name' => $v['Name']);
			}
		}
		else
		{
			// Error
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}

// madmimi
function snp_ml_get_madm_lists($ml_madm_username = '', $ml_madm_apikey = '')
{
    require_once SNP_DIR_PATH . '/include/madmimi/MadMimi.class.php';
    $list = array();
    if (
	    (snp_get_option('ml_madm_username') && snp_get_option('ml_madm_apikey')) ||
	    ($ml_madm_username && $ml_madm_apikey)
    )
    {
	try
	{
	    if ($ml_madm_username && $ml_madm_apikey)
	    {
		$mailer = new MadMimi($ml_madm_username, $ml_madm_apikey);
	    }
	    else
	    {
		$mailer	 = new MadMimi(snp_get_option('ml_madm_username'), snp_get_option('ml_madm_apikey'));
	    }
	    $lists	 = new SimpleXMLElement($mailer->Lists());
	    if ($lists->list)
	    {
		foreach ($lists->list as $l)
		{
		    $list[(string) $l->attributes()->{'name'}->{0}] = array('name' => (string) $l->attributes()->{'name'}->{0});
		}
	    }
	}
	catch (Exception $exc)
	{
	    
	}
    }
    if (count($list) == 0)
    {
	$list[0] = array('name' => 'Nothing Found...');
    }
    return $list;
}

// infusionsoft
function snp_ml_get_infusionsoft_lists($ml_inf_subdomain = '', $ml_inf_apikey = '')
{
    require_once SNP_DIR_PATH . '/include/infusionsoft/infusionsoft.php';

    $list = array();
    if (
	    (snp_get_option('ml_inf_subdomain') && snp_get_option('ml_inf_apikey')) ||
	    ($ml_inf_subdomain && $ml_inf_apikey)
    )
    {
	try
	{
	    if ($ml_inf_subdomain && $ml_inf_apikey)
	    {
		$infusionsoft	 = new Infusionsoft($ml_inf_subdomain, $ml_inf_apikey);
	    }
	    else
	    {
		$infusionsoft	 = new Infusionsoft(snp_get_option('ml_inf_subdomain'), snp_get_option('ml_inf_apikey'));
	    }
	    $fields = array('Id','GroupName');
	    $query = array('Id' => '%');
	    $result = $infusionsoft->data('query','ContactGroup',1000,0,$query,$fields);
	    if (is_array($result))
	    {
		foreach ($result as $l)
		{
		    $list[$l['Id']] = array('name' => $l['GroupName']);
		}
	    }
	}
	catch (Exception $exc)
	{
	    
	}
    }
    if (count($list) == 0)
    {
	$list[0] = array('name' => 'Nothing Found...');
    }
    return $list;
}
// egoi
function snp_ml_get_egoi_lists($ml_egoi_apikey='')
{
    require_once SNP_DIR_PATH . '/include/egoi/snp_egoi.php';
	$list = array();
	if (snp_get_option('ml_egoi_apikey') || $ml_egoi_apikey)
	{
		if (!$ml_egoi_apikey)
		{
			$ml_egoi_apikey = snp_get_option('ml_egoi_apikey');
		}  
		try
		{
                    $rest = new snp_egoi($ml_egoi_apikey);
                    $result = $rest->getLists();
			if (is_array($result))
			{
			    foreach ($result as $l)
			    {
				$list[$l['listnum']] = array('name' => $l['title']);
			    }
			}
		}
		catch (Exception $e)
		{
			//die($e->getMessage());
			// Error
		}
	}
	if (count($list) == 0)
	{
		$list[0] = array('name' => 'Nothing Found...');
	}
	return $list;
}