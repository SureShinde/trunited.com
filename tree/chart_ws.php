<?php

$db = new PDO('mysql:host=localhost;dbname=trunited_mage;charset=utf8mb4', 'root', 'CpEUQD3f');

//Get The ID for query
$id = $_GET['id'];

//Get The chart type
$type = $_GET['type'];


switch ($type) {
    case 'getOrg':
        $stmt = $db->prepare("
SELECT 
  g.PersonID as 'key',
  cC.customer_name as Person, 
  pC.Title as PersonTitle, 
  IFNULL(pC.Points, 0) as Points,
  DATE_FORMAT(cC.StartDate, '%m-%d-%Y') as StartDate,
  g.Level,
  if(g.PersonID = g.TreeRootID , null, g.ParentID) as parent,  
  tC.Color as color,
  CASE WHEN IFNULL(pC.Points, 0) = 0 THEN 'images/seed.png' ELSE LOWER(CONCAT('images/', pC.Title, pC.DepthCredit, '.png')) END as image,
  pC.DepthCredit
FROM tr_Genealogy g
LEFT JOIN tr_People pP on  g.ParentId = pP.PersonID
JOIN tr_People pC on  g.PersonID = pC.PersonID
JOIN (
  SELECT t.customer_id, t.customer_email, c.name as customer_name, c.created_time as StartDate, p.customer_id as parent_id, p.name as parent_name, p.email as parent_email 
  FROM traffiliateplus_tracking t 
  JOIN traffiliateplus_account p ON t.account_id = p.account_id
  JOIN traffiliateplus_account c ON t.customer_id = c.customer_id) cC on pC.PersonID = cC.customer_id
JOIN tr_Titles tC on pC.Title = tC.title
WHERE g.TreeRootID = ?
ORDER BY IF(g.TreeRootID=g.PersonID, 0, 1) , g.Level; ");

        $stmt->execute(array($id));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $datas = array();
        $persons = array();
        $reverse = array();
        $c = 0;
		$depthCredit = 0;
        foreach ($rows as $r) {
            $r['label'] = "" . $r['Person'] . "\n" . $r['Title'] . "\n" . $r['Points'] . ' Points' . "\nUID:" . $r['PersonID'];
			
			$width = strlen($r['label']) * 5;
			
			$r['labelWidth'] = $width;
			$r['pointBalance'] = $r['Points'];
			
			if($r['image'] == 'images/seed.png')
				$r['isSeed'] = true;
			else
				$r['isSeed'] = false;
			
            $persons[$c] = $r;
            $reverse[$r['key']] = $c;
            
			
			if($r['Level'] == 0)
				$depthCredit = $r['DepthCredit'];
			else
				$persons[$c]['displayingLevel'] = $depthCredit;
			$c++;
        }

        foreach ($persons as $i => $p) {
            if (isset($reverse[$persons[$i]['parent']])) {

                $persons[$i]['parent'] = $reverse[$persons[$i]['parent']];


                $persons[$i]['key'] = $reverse[$persons[$i]['key']];
            } else {
                $persons[$i]['key'] = 0;
                unset($persons[$i]['parent']);
            }
			
			if($persons[$i]['Level'] > $depthCredit)
			{
				$persons[$i]['color'] = "lightgrey";
			}
			//$persons[$i]['color'] = ($persons[$persons[$i]['parent']]['DepthCredit'] < $persons[$persons[$i]['parent']]['Level']) ? "#cccccc" : $persons[$persons[$i]['parent']]['Color'];
        }

        header('Content-Type: application/json');

        echo json_encode($persons);
        break;
		
    case 'people':

        $term = '%' . $_GET['term'] . '%';

        $stmt = $db->prepare("
            SELECT customer_id as value, name as label
            FROM traffiliateplus_account
            WHERE name LIKE ?
            ORDER BY name
          ");

        $stmt->execute(array($term));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($rows);
        break;
}


function generateTree($datas, $persons, $parent = 0, $depth = 0)
{

    if ($depth > 1000) return ''; // Make sure not to have an endless recursion
    $tree = array();

    $tree['color'] = ($persons[$parent]['DepthCredit'] < $persons[$parent]['Level']) ? "#cccccc" : $persons[$parent]['Color'];
    $tree['id'] = $persons[$parent]['PersonID'];//('.$persons[$parent]['PersonID'].') <br>
    $tree['label'] = "" . $persons[$parent]['Person'] . '<br>' . $persons[$parent]['PersonTitle'] . ' <br>' . $persons[$parent]['Points'] . ' Points';

    $tree['amount'] = $persons[$parent]['Points'];
    $tree['size'] = 1000;
    $tree['children'] = array();

    foreach ($datas as $k => $data) {
        unset($datas[$k]);
        if ($data['ParentId'] == $parent) {

            $children = generateTree($datas, $persons, $data['PersonID'], $depth + 1);

            if (count($children) > 0) {
                $tree['children'][$data['PersonID']] = $children;
            }

        }
    }

    sort($tree['children']);

    if (count($tree['children']) == 0) {
        unset($tree['children']);
    } else {
        //$a = /*$tree['amount'] */ 50 / count($tree['children']);
        foreach ($tree['children'] as $l => $c) {
            $tree['children'][$l]['amount'] = ($tree['children'][$l]['amount'] / $tree['amount']) * 25;
        }
    }
    return $tree;
}



