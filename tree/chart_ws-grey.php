<?php

$db = new PDO('mysql:host=localhost;dbname=dev_trunited;charset=utf8mb4', 'root', 'CpEUQD3f');

//Get The ID for query
$id = $_GET['id'];

//Get The chart type
$type = $_GET['type'];


switch ($type) {
    case 'bubble':

        $stmt = $db->prepare("SELECT g.* , 
                        pC.Title as PersonTitle, concat( cC.firstname,' ', cC.lastname ) as Person, 
                        pP.Title as ParentTitle, concat( cP.firstname,' ', cP.lastname ) as Parent,
                        -- ROUND(RAND()*(200-50)+50) Points,
                        ROUND(IFNULL(o.grand_total, 0) / 2) as Points,
                        pC.DepthCredit,
                        tC.Color
            FROM ci_geneology g
            LEFT JOIN ci_people pP on  g.ParentId = pP.PersonID
            LEFT JOIN ci_customer cP on pP.PersonID = cP.magentocustomerid
            JOIN ci_people pC on  g.PersonID = pC.PersonID
            JOIN ci_customer cC on pC.PersonID = cC.magentocustomerid
            JOIN ci_titles tC on pC.Title = tC.title
            LEFT JOIN sales_order o ON cC.orderid = o.entity_id
            WHERE g.TreeRootID =  ? order by if(g.TreeRootID=g.PersonID, 0, 1) , g.PersonID;");


        $stmt->execute(array($id));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $datas = array();
        $persons = array();
        foreach ($rows as $r) {
            $datas[] = $r;
            $persons[$r['PersonID']] = $r;
        }
        $tree = (generateTree($persons, $persons, $id));

        header('Content-Type: application/json');
        echo json_encode($tree);

        die;
        break;
    case 'getOrg':
        $stmt = $db->prepare("
SELECT 
  g.* , 
  pC.Title as PersonTitle, concat( cC.firstname,' ', cC.lastname ) as Person, 
  pP.Title as ParentTitle, concat( cP.firstname,' ', cP.lastname ) as Parent,
  ROUND(IFNULL(o.grand_total, 0) / 2) as Points,
  if(g.PersonID = g.TreeRootID , null, g.ParentID) as parent,
  g.PersonID as `key`,
  pC.DepthCredit,
  tC.Color as color,
  CASE WHEN ROUND(IFNULL(o.grand_total, 0) / 2) = 0 THEN '/images/seed.png' ELSE 
    LOWER(CONCAT('/images/', pC.Title, pC.DepthCredit, '.png')) END as image
FROM ci_geneology g
LEFT JOIN ci_people pP on  g.ParentId = pP.PersonID
LEFT JOIN ci_customer cP on pP.PersonID = cP.magentocustomerid
JOIN ci_people pC on  g.PersonID = pC.PersonID
JOIN ci_customer cC on pC.PersonID = cC.magentocustomerid
JOIN ci_titles tC on pC.Title = tC.title
LEFT JOIN sales_order o ON cC.orderid = o.entity_id
WHERE g.TreeRootID =  ? 
ORDER BY IF(g.TreeRootID=g.PersonID, 0, 1) , g.PersonID; ");

        $stmt->execute(array($id));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $datas = array();
        $persons = array();
        $reverse = array();
        $c = 0;
		
		$depthCredit = 0;
        foreach ($rows as $r) {
            $r['label'] = "" . $r['Person'] . "\n" . $r['Title'] . "\n" . $r['Points'] . ' Points' . "\nUID:" . $r['PersonID'];
            $persons[$c] = $r;
            $reverse[$r['key']] = $c;
            $c++;
			
			if($r['Level'] == 0)
			{
				$depthCredit = $r['DepthCredit'];
			}
        }

        foreach ($persons as $i => $p) {
            if (isset($reverse[$persons[$i]['parent']])) {

                $persons[$i]['parent'] = $reverse[$persons[$i]['parent']];


                $persons[$i]['key'] = $reverse[$persons[$i]['key']];
            } else {
                $persons[$i]['key'] = 0;
                unset($persons[$i]['parent']);
            }
			
			if($persons[$i]['Level'] > $depthCredit + 1)
			{
				$persons[$i]['color'] = "lightgrey";
			}
			//$persons[$i]['color'] = ($persons[$persons[$i]['parent']]['DepthCredit'] < $persons[$persons[$i]['parent']]['Level']) ? "#cccccc" : $persons[$persons[$i]['parent']]['Color'];
        }

        header('Content-Type: application/json');

        echo json_encode($persons);
        break;
	
	    case 'big':
        $stmt = $db->prepare("
                        SELECT g.* , 
                                    pC.Title as PersonTitle, concat( cC.firstname,' ', cC.lastname ) as Person, 
                                    pP.Title as ParentTitle, concat( cP.firstname,' ', cP.lastname ) as Parent,
                                    -- ROUND(RAND()*(200-50)+50) Points,
                                    ROUND(IFNULL(o.grand_total, 0) / 2) as Points,
                                    if(g.PersonID = g.TreeRootID , null, g.ParentID) as parent,
                                    g.PersonID as `key`,
                                    pC.DepthCredit,
                                    tC.Color as color
                        FROM ci_geneology g
                        LEFT JOIN ci_people pP on  g.ParentId = pP.PersonID
                        LEFT JOIN ci_customer cP on pP.PersonID = cP.magentocustomerid
                        JOIN ci_people pC on  g.PersonID = pC.PersonID
                        JOIN ci_customer cC on pC.PersonID = cC.magentocustomerid
                        JOIN ci_titles tC on pC.Title = tC.title
                        LEFT JOIN sales_order o ON cC.orderid = o.entity_id
                        WHERE g.TreeRootID =  ? order by if(g.TreeRootID=g.PersonID, 0, 1) , g.PersonID; ");

        $stmt->execute(array($id));

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $datas = array();
        $persons = array();
        $reverse = array();
        $c = 0;
        foreach ($rows as $r) {
            $r['label'] = "" . $r['Person'] . "\n" . $r['Title'] . "\n" . $r['Points'] . ' Points' . "\nUID:" . $r['PersonID'];
            $persons[$c] = $r;
            $reverse[$r['key']] = $c;
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
        }

        header('Content-Type: application/json');

        echo json_encode($persons);
        break;
		
    case 'people':

        $term = '%' . $_GET['term'] . '%';

        $stmt = $db->prepare("
          select cC.magentocustomerid as value,   concat( cC.firstname,' ', cC.lastname ) as label  
          from   ci_customer cC 
          where 
            concat( cC.firstname,' ', cC.lastname ) like  ? 
          order by concat( cC.firstname,' ', cC.lastname ) 
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



