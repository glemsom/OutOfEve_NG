<?php

    class contracts extends Plugin {
        var $name = 'Contracts';
        var $level = 1;

        function contracts($db, $site) {
            $this->Plugin($db, $site);

            $this->site->plugins['mainmenu']->addLink('main', 'Contracts', '?module=contracts', 'icon17_02');
/*
            if (($this->site->plugins['mainmenu']->hasGroup('corp')) 
                    && ($this->site->character->corpMember->hasRole('corpRoleDirector')
                        || $this->site->plugins['users']->hasForcedMenu('corpAssets')))
                $this->site->plugins['mainmenu']->addLink('corp', 'Assets', '?module=assets&corp=1', 'icon07_13');
*/
        }

        function getContent() {
            if (!isset($_GET['p']))
                $_GET['p'] = 0;

            if (isset($_GET['corp'])) {
                $this->site->character->corporation->loadAssets();
                $fullAssetList = $this->site->character->corporation->assets;
            } else {
                $this->site->character->loadContracts();
                $fullContractList = $this->site->character->contracts;
            }

/*
    object(eveContract)[396]
      public 'contractID' => string '599307xx' (length=8)
      public 'issuerID' => string '918502xx' (length=8)
      public 'issuerCorpID' => string '980100xx' (length=8)
      public 'assigneeID' => string '980100xx' (length=8)
      public 'acceptorID' => string '980100xx' (length=8)
      public 'startStationID' => string '610007xx' (length=8)
      public 'endStationID' => string '610007xx' (length=8)
      public 'type' => string 'ItemExchange' (length=12)
      public 'status' => string 'Completed' (length=9)
      public 'title' => string '' (length=0)
      public 'forCorp' => string '0' (length=1)
      public 'availability' => string 'Private' (length=7)
      public 'dateIssued' => string '2012-09-30 11:43:30' (length=19)
      public 'dateExpired' => string '2012-10-14 11:43:30' (length=19)
      public 'dataAccepted' => null
      public 'dateCompleted' => string '2012-09-30 15:12:15' (length=19)
      public 'numDays' => string '0' (length=1)
      public 'price' => string '40301906.00' (length=11)
      public 'reward' => string '0.00' (length=4)
      public 'collateral' => string '0.00' (length=4)
      public 'buyout' => string '0.00' (length=4)
      public 'volume' => string '309197.75' (length=9)
      public 'dateAccepted' => string '2012-09-30 15:12:15' (length=19)
*/


	$type = $_GET["type"];
	$status = $_GET["status"];
	$time = $_GET["time"];

	if ($type == "" && $status == "" && $time == "")
	{
		$contractList = $fullContractList;
	} else {
		$contractList = array();
		foreach ($fullContractList as $contract)
		{
			if ($status != "" && $status != $contract->status)
				continue;

                        if ($type != "" && $type != $contract->type)
                                continue;

			if ($time != "")
			{
				//time compare 2012-09-30 11:43:30
				$unix = strtotime($contract->dateIssued);
				$now = time();
				$diff = $now - $unix;
				if ($diff > ($time * 24 * 60 * 60))
					continue;
			}

			$contractList[] = $contract;
		}
	}

	usort($contractList, 'sortContractList');
        $contractList = objectToArray($contractList, array('DBManager', 'eveDB'));


$vars = array('contracts' => $contractList, $vars, 'pageCount' => 0,
              'pageNum' => 0, 'nextPage' => 1, 'prevPage' => 1, 'corp' => isset($_GET['corp']));


$vars['typeSelect']     = array(''=>'Any','ItemExchange' => 'ItemExchange','Courier' => 'Courier','Auction' => 'Auction');
$vars['selectedType']   = $type;

$vars['statusSelect']   = array(''=>'Any','Completed'=>'Completed','Deleted'=>'Deleted','Outstanding'=>'Outstanding','InProgress'=>'InProgress');
$vars['selectedStatus'] = $status;

$vars['timeSelect']     = array(''=>'Unlimited',7=>'1 week',31=>'1 month');
$vars['selectedTime']   = $time;


                return $this->render('contracts', $vars);
        }

    }

function sortContractList($a, $b)
{
    if ($a->dateIssued == $b->dateIssued) {
        return 0;
    }
    return ($a->dateIssued < $b->dateIssued) ? 1 : -1;
}

?>
