<?php
/*
** Monitor Api
** junying.guo@gmail.com
** 2014-01-02
**/

require_once dirname(__FILE__).'/include/config.inc.php';

$action = $_GET['action'];
if($action == 'getdata')
    getAllItems();
elseif($action == 'save')
    save();
elseif($action == 'delete')
    delete();
elseif($action == 'lastvalue')
    getLastvalue($_GET['solution']);
elseif($action == 'getsolution')
    getAllSolution();
elseif($action == 'getsolutionitems')
    getSolutionItems($_GET['solution']);

    
function getAllItems()
{
    $db_hosts = array();
    $db_hostids = array();

    $sql = 'SELECT h.hostid,h.name FROM `hosts` h WHERE h.name NOT LIKE "%Template%" AND h.name NOT LIKE "%#%"';
    $db_host_res = DBselect($sql);
    $json_str = '[';
    while($db_host = DBfetch($db_host_res)) {
        $json_str = $json_str.'{"text": "'.$db_host['name'].'","cls": "folder","expanded": false,"children": ';
        $tmpsql = 'SELECT DISTINCT h.host AS hostname,h.hostid,i.`itemid`, i.`name`, i.`key_` FROM  `hosts` h, items i '.
            'WHERE 1=1 '.
            'AND h.`host` NOT LIKE "%#%" '.
            'AND i.`name` NOT LIKE "%#%" '.
            'AND i.`status` = 0 '.
            'AND h.hostid='.$db_host['hostid'].' '.
            'AND h.hostid=i.hostid '.
            'AND (i.status=0 OR i.status=3) '.
            'AND i.flags IN ("0","4") '.
            'AND h.hostid="'.$db_host['hostid'].'" '.
            'ORDER BY i.name,i.itemid';
        $db_item_res = DBselect($tmpsql);
        $json_str = $json_str.'[';
        while($db_item = DBfetch($db_item_res)) {
            $name = $db_item['name'];
            if(strpos($db_item['name'],"$") != false){
                $key_str = substr($db_item['key_'],strpos($db_item['key_'],"[") + 1, -1);
                $key_arr = explode(',',$key_str);
                $search = array();
                for ($i = 1; $i <= count($key_arr); $i++) {
                    $search[] = "$".$i;
                }
                $name = str_replace($search,$key_arr,$db_item['name']);
            }
            $json_str = $json_str.'{"hostid":"'.$db_item['hostid'].'","id":"'.$db_item['itemid'].'","text": "'.$name.'","leaf": true,"checked": false},';
        }
        $json_str = substr($json_str, 0, -1).']},';
    }
    $json_str = substr($json_str, 0, -1).']';
    echo $json_str;
}

function save()
{
    $solution = $_POST[solution];
    // delete old rows
    $sql = 'DELETE FROM monitor WHERE `solution` = "'.$solution.'"';
    DBselect($sql);
    // insert new rows
    $sql = 'INSERT INTO monitor(`itemid`,`itemname`,`solution`,`date`,`hostid`) VALUES';
    for ($i = 0; $i < count($_POST[data]); $i++) {
        $sql.='('.$_POST[data][$i].',"'.$_POST[name][$i].'","'.$solution.'",NOW(),"'.$_POST[hostid][$i].'"),';
    }
    //foreach($_POST[data] as $key => $value){
    //    $sql.='('.$value.',"'.$solution.'","'.$solution.'",NOW()),';
    //}
    $sql = substr($sql, 0, -1);
    DBselect($sql);
    echo $sql;
}

function delete()
{
    $solution = $_POST[solution];
    $sql = 'DELETE FROM monitor WHERE `solution` = "'.$solution.'"';
    DBselect($sql);
    echo $sql;
}

function getLastvalue($solution)
{
    $solution_arr = explode(',',$solution);
    $solutions = '(';
    foreach ($solution_arr as $s) {
        $solutions.='"'.$s.'",';
    }
    $solutions = substr($solutions, 0, -1).')';
    
    $sql = 'SELECT h.name hostname, m.itemname, m.lastvalue '.
            'FROM `monitor` m '.
              //'LEFT JOIN `items` i '.
              //  'ON m.itemid = i.itemid '.
              'LEFT JOIN `hosts` h '.
                'ON m.hostid = h.hostid '.
            'WHERE solution in '.$solutions.'';
    //echo $sql ;
    $result = '';
    $db_lastvalue_res = DBselect($sql);
    while($db_lastvalue = DBfetch($db_lastvalue_res)) {
        $result .= $db_lastvalue['hostname'].':'.$db_lastvalue['itemname'].','.$db_lastvalue['lastvalue'].';';
    }
    echo $result;
}

    
function getSolutionItems($solution)
{
    $items = array();
    $tmpsql = 'SELECT m.`itemid`,m.`itemname` AS itemname,h.`name` AS hostname, i.`key_` FROM monitor m '.
        'LEFT JOIN items i ON  i.`itemid` = m.`itemid` '.
        'LEFT JOIN `hosts` h ON h.`hostid` = i.`hostid` '.
        'WHERE m.solution = "'.$solution.'"';
    //echo $tmpsql;
    $db_items_res = DBselect($tmpsql);
    
    while($db_item = DBfetch($db_items_res)) {
        /*$name = $db_item['itemname'];
        if(strpos($db_item['itemname'],"$") != false){
            $key_str = substr($db_item['key_'],strpos($db_item['key_'],"[") + 1, -1);
            $key_arr = explode(',',$key_str);
            $search = array();
            for ($i = 1; $i <= count($key_arr); $i++) {
                $search[] = "$".$i;
            }
            $name = str_replace($search,$key_arr,$db_item['itemname']);
        }*/
        if($items[$db_item['hostname']] == null){
            $items[$db_item['hostname']] = array();
        }
        $items[$db_item['hostname']][$db_item['itemid']] = $db_item['itemname'];//$name;
    }
    $json_str = '[';
    foreach ($items as $hostname => $host) {
        $json_str = $json_str.'{"text": "'.$hostname.'","cls": "folder","expanded": true,"children": [';
        foreach ($host as $itemid => $item) {
            $json_str = $json_str.'{"id":"'.$itemid.'","text": "'.$item.'","leaf": true,"checked": true},';
        }
        $json_str = substr($json_str, 0, -1).']},';
    }
    $json_str = substr($json_str, 0, -1).']';
    
    echo $json_str;
}


function getAllSolution()
{
    $sql = 'SELECT DISTINCT solution,`date` FROM monitor ';
    $result = '{"items":[';
    $db_solution_res = DBselect($sql);
    while($db_solution = DBfetch($db_solution_res)) {
        $result .= '{"solution":"'.$db_solution['solution'].'","date":"'.$db_solution['date'].'"},';
    }
    $result = substr($result, 0, -1).']}';
    echo $result;
}

?>
