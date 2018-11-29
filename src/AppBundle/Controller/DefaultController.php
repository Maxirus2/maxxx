<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\yacheika;
use AppBundle\Entity\onhand;
use AppBundle\Entity\items;
use AppBundle\Entity\process;
use AppBundle\Entity\InventDim;
use AppBundle\Entity\inventtrans;
use AppBundle\Entity\Users;
use AppBundle\Entity\wherehouse;
use AppBundle\Entity\car;
use AppBundle\Entity\tasksusers;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage") //good
     */
    public function indexAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
		
		
		if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
			if($_SESSION['role']==3){
				header('Location: /lk/'.$_SESSION['name']);
	            exit;
			}
////////////////////////////////////////////////////////////////////////////
//начало таблица на складах
	$products = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->getQuery()
    ->getResult();	

if(empty($products)){
$next = 0;
}
else{
$next = 1;
}

	 $types = $this->getDoctrine()->getRepository('AppBundle:items')->findAll();
	 for($i=0;$i<count($types);$i++){
		 $array_of_types[$i]= $types[$i]->getName();
	 }
	 
	 for($i=0;$i<count($types);$i++){	
	 $result_array_items[$i][1]=0;
       for($j=0;$j<count($products);$j++){		
	   	if($array_of_types[$i]==$products[$j]['name']){
			$result_array_items[$i][0]=$products[$j]['name'];
			$result_array_items[$i][1]=$result_array_items[$i][1]+$products[$j]['quarty'];
			$result_array_items[$i][2]=$products[$j]['type'];
		}

       }
if($result_array_items[$i][1]==0){
unset($result_array_items[$i]);
}	
     }
sort($result_array_items);
 //конец таблица на складах     
	  
 

//Начало графика	   
$inventtrans = $this->getDoctrine()->getRepository('AppBundle:inventtrans')->findAll();
	  for($i=1;$i<=12;$i++){
	$res1[$i]=0;
	$res2[$i]=0;
	
}
	  $year = date('Y'); 
	    if(!empty($inventtrans)){
			$array1 = [
    "1" => "Январь",
    "2" => "Февраль",
	"3" => "Март",
    "4" => "Апрель",
	"5" => "Май",
    "6" => "Июнь",
	"7" => "Июль",
    "8" => "Август",
	"9" => "Сентябрь",
    "10" => "Октябрь",
	"11" => "Ноябрь",
    "12" => "Декабрь",
];


for($i=1;$i<=12;$i++){
	for($j=0;$j<count($inventtrans);$j++){
		$item_id=$this->getDoctrine()->getRepository('AppBundle:items')->findBy(array('id' => $inventtrans[$j]->getItemid()));
		if($item_id[0]->getType()=="Сырье"){
			$date = $inventtrans[$j]->getDate();
			$data = explode(".",$date);
			$today=date("m.d.y"); 
			$today1 = explode(".",$today);
			if((int)$data[1]==$i&&$today1[2]==$data[2]){
				if($inventtrans[$j]->getDirection()=="IN"){
	            $res1[$i]=$res1[$i]+$inventtrans[$j]->getQty();
				}
				else{
				$res1[$i]=$res1[$i]-$inventtrans[$j]->getQty();	
				}
			}
			
		}
		if($item_id[0]->getType()=="Продукция"){
			$date = $inventtrans[$j]->getDate();
			$data = explode(".",$date);
			$today=date("m.d.y"); 
			$today1 = explode(".",$today);
			if((int)$data[1]==$i&&$today1[2]==$data[2]){
				
					if($inventtrans[$j]->getDirection()=="IN"){
	            $res2[$i]=$res2[$i]+$inventtrans[$j]->getQty();
				}
				else{
				$res2[$i]=$res2[$i]-$inventtrans[$j]->getQty();	
				}
			}
			
		}
		
		
	}	
}
	
		}
//конец графика - выходные параметры res1, res2	   

//начало заполненность складов
$sklads = $this->getDoctrine()->getRepository('AppBundle:wherehouse')->findAll();	
   
for($i=0;$i<32;$i++){
	$profit_yach_a1[$i]=0;
	$profit_yach_a2[$i]=0;
	$profit_yach_c1[$i]=0;
	$profit_yach_c2[$i]=0;
}	   

for($i=0;$i<count($products);$i++){
  for($j=0;$j<32;$j++){	 
    $summa=0;
	 if($products[$i]['nameYacheika']=='А1.'.($j+1)){
      $summa = 	$summa+$products[$i]['quarty'];
	  $profit_yach_a1[$j]=round($summa /$products[$i]['countY'],4);
	 }  	      
	  if($products[$i]['nameYacheika']=='А2.'.($j+1)){
      $summa = 	$summa+$products[$i]['quarty'];
	  $profit_yach_a2[$j]=round($summa /$products[$i]['countY'],4);
	 }  
	  if($products[$i]['nameYacheika']=='С1.'.($j+1)){
      $summa = 	$summa+$products[$i]['quarty'];
	  $profit_yach_c1[$j]=round($summa /$products[$i]['countY'],4);
	 }  
	  if($products[$i]['nameYacheika']=='С2.'.($j+1)){
      $summa = 	$summa+$products[$i]['quarty'];
	  $profit_yach_c2[$j]=round($summa /$products[$i]['countY'],4);
	 }  
   
  }	 
}

$result_sklad_a1=0;
$result_sklad_a2=0;
$result_sklad_c1=0;
$result_sklad_c2=0;

for($i=0;$i<32;$i++){
	$result_sklad_a1=$result_sklad_a1+$profit_yach_a1[$i];
	$result_sklad_a2=$result_sklad_a2+$profit_yach_a2[$i];
	$result_sklad_c1=$result_sklad_c1+$profit_yach_c1[$i];
	$result_sklad_c2=$result_sklad_c2+$profit_yach_c2[$i];
}
$result_sklad_a1=(round($result_sklad_a1/32,2))*100;
$result_sklad_a2=(round($result_sklad_a2/32,2))*100;
$result_sklad_c1=(round($result_sklad_c1/32,2))*100;
$result_sklad_c2=(round($result_sklad_c2/32,2))*100;


//конец заполненность складов	   
////////////////////////////////////////////////////////////////////////////		

		return $this->render('default/index.html.twig', array(''
					. 'result_array_items_kol' => count($result_array_items)-1, ''
					. 'year' => $year, ''
                    . 'next' => $next, ''
					. 'res1' => $res1, ''
		            . 'res2' => $res2, ''
					. 'result_sklad_a1' => $result_sklad_a1, ''
					. 'result_sklad_a2' => $result_sklad_a2, ''
		            . 'result_sklad_c1' => $result_sklad_c1, ''
					. 'result_sklad_c2' => $result_sklad_c2, ''
					. 'result_array_items' => $result_array_items, ''
				    . 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
    }
	header('Location: /auth');
	exit;
	return $this->render('default/autorisation.html.twig');
	
	}
	
	 /**
     * @Route("/tasks", name="tasks")
     */
    public function tasksAction(Request $request)
    {
		
   
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
     if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
		$process = $this->getDoctrine()->getRepository('AppBundle:process')->findAll();
		$process_res= null;
        for($i=0;$i<count($process);$i++){
			$process_words=explode(" ",$process[$i]->getTypes());
			if($process_words[0]=="Отгрузка"){
				$process_res[$i]="ok";
			}else
			{
				$process_res[$i]="no";
			}
			
		}
//начало обработка взятия задания
		if($request->get('task')&&$request->get('vibor')){

    $entityManager = $this->getDoctrine()->getManager();
    $task = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));
if( $task->getWorker()==" "){
    $task->setWorker($_SESSION['name']);
    $task->setStatus("Выполняется");
    $entityManager->flush();  
}
header('Location: /tasks');
	exit;  
 
}
//конец обработка взятия задания	

//начало обработка отказа от задания	
	if($request->get('task')&&$request->get('destroy')){
    $entityManager = $this->getDoctrine()->getManager();
    $task = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));
    $task->setWorker(" ");
    $task->setStatus("Никем не выбрано");
    $entityManager->flush();   
   
	header('Location: /tasks');
	exit;
}
//конец обработка отказа от задания

//начало внутрескладское перемещение
if($request->get('task')&&$request->get('complite')&&$request->get('decs')&&$request->get('type')=="Внутрескладское перемещение"){
$words = explode(" ",$request->get('decs'));
$out_yach =$words[2];
$in_yach =$words[4];
$kol =$words[5];

$productOut = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('dp.nameYacheika = :nameYacheika1')
	->setParameter('nameYacheika1', $out_yach)
    ->getQuery()
    ->getResult();	

$productIn = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('dp.nameYacheika = :nameYacheika1')
	->setParameter('nameYacheika1', $in_yach)
    ->getQuery()
    ->getResult();

if(empty($productIn)){
$entityManager = $this->getDoctrine()->getManager();
$task = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));
$result = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findBy(array('nameYacheika' => trim($in_yach)));

        $product = new InventDim();
        $product->setColor($task->getColor());
        $product->setSize($task->getSize());
        $product->setConfig($task->getConfig());
        $product->setWarehouseId($result[0]->getId());
        $product->setQuarty($kol);
        $entityManager->persist($product);
        $entityManager->flush();

$inventdim = $this->getDoctrine()->getRepository('AppBundle:InventDim')->findBy(array('warehouseId' => $result[0]->getId()));

        $product = new onhand();
        $product->setItemid($productOut[0]['itemid']);
        $product->setInventdimId($inventdim[0]->getId());      
        $entityManager->persist($product);
        $entityManager->flush();

if($productOut[0]['quarty']-$kol==0){
$entityManager = $this->getDoctrine()->getManager();
$invent_delete = $entityManager->getRepository('AppBundle:InventDim')->find($productOut[0]['inventdimId']);
$onhand_delete = $entityManager->getRepository('AppBundle:onhand')->findOneBy(array('inventdimId' => $productOut[0]['inventdimId']));
$entityManager->remove($invent_delete);
$entityManager->remove($onhand_delete);
$entityManager->flush();
}
else
{
$entityManager = $this->getDoctrine()->getManager();
$invent_change = $entityManager->getRepository('AppBundle:InventDim')->find($productOut[0]['inventdimId']);
$invent_change->setQuarty($invent_change->getQuarty()-$kol);
$entityManager->flush(); 
}
                     }
else
                     {
$entityManager = $this->getDoctrine()->getManager();
$invent_out = $entityManager->getRepository('AppBundle:InventDim')->find($productOut[0]['inventdimId']);
$invent_in = $entityManager->getRepository('AppBundle:InventDim')->find($productIn[0]['inventdimId']);

if($productOut[0]['quarty']-$kol==0){
$entityManager = $this->getDoctrine()->getManager();
$invent_delete = $entityManager->getRepository('AppBundle:InventDim')->find($productOut[0]['inventdimId']);
$onhand_delete = $entityManager->getRepository('AppBundle:onhand')->findOneBy(array('inventdimId' => $productOut[0]['inventdimId']));
$entityManager->remove($invent_delete);
$entityManager->remove($onhand_delete);
$invent_in->setQuarty($invent_in->getQuarty()+$kol);
$entityManager->flush();
}
else
{
$invent_out->setQuarty($invent_out->getQuarty()-$kol);
$invent_in->setQuarty($invent_in->getQuarty()+$kol);
$entityManager->flush(); 
}

                     }
					 
					 
$entityManager = $this->getDoctrine()->getManager();
$task_delete = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));		
			 
$taskk = new tasksusers();
        $taskk->setType($task_delete->getTypes());
        $taskk->setName($task_delete->getLotid());
        $taskk->setData(date("d.m.y"));
        $taskk->setNameUser($_SESSION['name']);
        $entityManager->persist($taskk);
        $entityManager->flush();

					 

$entityManager->remove($task_delete);
$entityManager->flush();


        $history = new inventtrans();
        $history->setDirection("OUT");
        $history->setDate(date("d.m.y"));
        $history->setItemId($productOut[0]['itemid']);
        $history->setInventdimId($productOut[0]['inventdimId']);
        $history->setQty($kol);
        $entityManager->persist($history);
        $entityManager->flush();

$result = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findBy(array('nameYacheika' => trim($in_yach)));
$inventdim = $this->getDoctrine()->getRepository('AppBundle:InventDim')->findBy(array('warehouseId' => $result[0]->getId()));

        $history = new inventtrans();
        $history->setDirection("IN");
        $history->setDate(date("d.m.y"));
        $history->setItemId($productOut[0]['itemid']);
        $history->setInventdimId($inventdim[0]->getId());
        $history->setQty($kol);
        $entityManager->persist($history);
        $entityManager->flush();

header('Location: /tasks');
	exit;
}
//конец внутрескладское перемещение
//начало отгрузки
if($request->get('task')&&$request->get('complite')&&$request->get('decs')&&$request->get('type')){
	$cheak=explode(" ",$request->get('type'));
	if($cheak[0]=="Отгрузка"&&$cheak[1]=="из"&&$cheak[2]=="склада"){
			$products_task_2 = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
	->andWhere('ep.nameSklad = :name')
	->setParameter('name', $cheak[3])
    ->getQuery()
    ->getResult();
		
	$tasc = explode(" ",$request->get('decs'));
	$kol_trans = (count($tasc)-1)/8;
$hag=0;
for($i=0;$i<$kol_trans;$i++){
$zadanie[$i]['sektor'] = $tasc[5+$hag];
$zadanie[$i]['count'] =$tasc[2+$hag]; 
$hag=$hag+8;
}

for($i=0;$i<$kol_trans;$i++){
	for($j=0;$j<count($products_task_2);$j++){
	if($products_task_2[$j]['nameYacheika']==$zadanie[$i]['sektor']){
	$entityManager = $this->getDoctrine()->getManager();
    $product = $entityManager->getRepository('AppBundle:InventDim')->find($products_task_2[$j]['inventdimId']);
	$itemid = $entityManager->getRepository('AppBundle:onhand')->findOneBy(array('inventdimId' => $products_task_2[$j]['inventdimId']));
	if($product->getQuarty()-$zadanie[$i]['count']==0){
		$product1 = $entityManager->getRepository('AppBundle:onhand')->findOneBy(array('inventdimId' => $products_task_2[$j]['inventdimId']));
		$entityManager->remove($product1);
		$entityManager->remove($product);
        $entityManager->flush();
		$history = new inventtrans();
        $history->setDirection("OUT");
        $history->setDate(date("d.m.y"));
        $history->setItemId($itemid->getItemid());
        $history->setInventdimId($products_task_2[$j]['inventdimId']);
        $history->setQty($zadanie[$i]['count']);
        $entityManager->persist($history);
        $entityManager->flush();
	}
	else{
	$product->setQuarty($product->getQuarty()-$zadanie[$i]['count']);
    $entityManager->flush();
	$history = new inventtrans();
        $history->setDirection("OUT");
        $history->setDate(date("d.m.y"));
        $history->setItemId($itemid->getItemid());
        $history->setInventdimId($products_task_2[$j]['inventdimId']);
        $history->setQty($zadanie[$i]['count']);
        $entityManager->persist($history);
        $entityManager->flush();
	}
	
	}
	}
}
$entityManager = $this->getDoctrine()->getManager();
$task_delete = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));

$taskk = new tasksusers();
        $taskk->setType($task_delete->getTypes());
        $taskk->setName($task_delete->getLotid());
        $taskk->setData(date("d.m.y"));
        $taskk->setNameUser($_SESSION['name']);
        $entityManager->persist($taskk);
        $entityManager->flush();

		
$entityManager->remove($task_delete);
$entityManager->flush();
header('Location: /tasks');
	exit;
	}
}
//конец отгрузки
//начало приемки
if($request->get('task')&&$request->get('complite')&&$request->get('decs')&&$request->get('type')=="Приемка"){

$task = explode(" ",$request->get('decs'));
$kol_trans = (count($task)-1)/8;
$hag=0;

for($i=0;$i<$kol_trans;$i++){
$zadanie[$i]['sektor'] = $task[7+$hag];
$zadanie[$i]['count'] =$task[3+$hag]; 
$hag=$hag+8;
}
$entityManager = $this->getDoctrine()->getManager();
$task_Repository = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));

for($i=0;$i<count($zadanie);$i++){
$productIn = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('dp.nameYacheika = :nameYacheika1')
	->setParameter('nameYacheika1', $zadanie[$i]['sektor'])
    ->getQuery()
    ->getResult();

if(empty($productIn)){
$result = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findBy(array('nameYacheika' => trim($zadanie[$i]['sektor'])));
 $product = new InventDim();
        $product->setColor($task_Repository->getColor());
        $product->setSize($task_Repository->getSize());
        $product->setConfig($task_Repository->getConfig());
        $product->setWarehouseId($result[0]->getId());
        $product->setQuarty($zadanie[$i]['count']);
        $entityManager->persist($product);
        $entityManager->flush();

$inventdim = $this->getDoctrine()->getRepository('AppBundle:InventDim')->findBy(array('warehouseId' => $result[0]->getId()));
$itemid = $this->getDoctrine()->getRepository('AppBundle:items')->findBy(array('name' => $task_Repository->getName()));

        $product = new onhand();
        $product->setItemid($itemid[0]->getId());
        $product->setInventdimId($inventdim[0]->getId());      
        $entityManager->persist($product);
        $entityManager->flush();

$history = new inventtrans();
        $history->setDirection("IN");
        $history->setDate(date("d.m.y"));
        $history->setItemId($itemid[0]->getId());
        $history->setInventdimId($inventdim[0]->getId());
        $history->setQty($zadanie[$i]['count']);
        $entityManager->persist($history);
        $entityManager->flush();

}
else
{
$entityManager = $this->getDoctrine()->getManager();
$invent_change = $entityManager->getRepository('AppBundle:InventDim')->find($productIn[0]['inventdimId']);
$invent_change->setQuarty($invent_change->getQuarty()+$zadanie[$i]['count']);
$entityManager->flush(); 
$history = new inventtrans();
        $history->setDirection("IN");
        $history->setDate(date("d.m.y"));
        $history->setItemId($productIn[0]['itemid']);
        $history->setInventdimId($productIn[0]['inventdimId']);
        $history->setQty($zadanie[$i]['count']);
        $entityManager->persist($history);
        $entityManager->flush();
}


        
}
$entityManager = $this->getDoctrine()->getManager();
$task_delete = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));

$taskk = new tasksusers();
        $taskk->setType($task_delete->getTypes());
        $taskk->setName($task_delete->getLotid());
        $taskk->setData(date("d.m.y"));
        $taskk->setNameUser($_SESSION['name']);
        $entityManager->persist($taskk);
        $entityManager->flush();

		
$entityManager->remove($task_delete);
$entityManager->flush();


header('Location: /tasks');
exit;
}
//конец приемки

//начало удаление таска
if($request->get('task')&&$request->get('destroed')){
$entityManager = $this->getDoctrine()->getManager();
$task_delete = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));
$entityManager->remove($task_delete);
$entityManager->flush();
header('Location: /tasks');
	exit;
}
//конец удаления таска


        return $this->render('default/tasks.html.twig', array(''
				     . 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name'], ''
					. 'process_res' => $process_res, ''
	                . 'process' => $process));
					
	 }
	 
	 header('Location: /auth');
	exit;
    }
	
		 /**
     * @Route("/skan", name="skan") //good
     */
    public function skanAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
      if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
if(empty($_SESSION['sklad'])){
		  $_SESSION['sklad']=$request->get('type');
}
if(empty($_SESSION['idtask'])){
		  $_SESSION['idtask']=$request->get('idtask');
}
        return $this->render('default/skan.html.twig', array(''
                    . 'type' => $request->get('type'), ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
	  }
	  header('Location: /auth');
	  exit;
    }
	
			 /**
     * @Route("/transaction", name="transaction") //good
     */
    public function transactionAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
      if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
if($_SESSION['role']==3){
				header('Location: /lk/'.$_SESSION['name']);
	            exit;
			}
      $inventtrans = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.direction,p.date,p.qty,cp.name')
    ->from('AppBundle:inventtrans', 'p')
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemId = cp.id")
    ->getQuery()
    ->getResult();	

		  $inventtrans = array_reverse($inventtrans);
		  


        return $this->render('default/transaction.html.twig', array(''
					. 'role' => $_SESSION['role'], ''
					. 'inventtrans' => $inventtrans, ''
					. 'login' => $_SESSION['name']));
	  }
	  header('Location: /auth');
	  exit;
    }
	
	
	
	
		 /**
     * @Route("/auth", name="auth") //good
     */
    public function authAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
		if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
			header('Location: //');
            exit;
		}
		$error="";
      if($request->get('username')&&$request->get('password')){
		$result = $this->getDoctrine()->getRepository('AppBundle:Users')->findBy(array('name' => $request->get('username'),'pass' => md5($request->get('password'))));
		if(!empty($result)){
				if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
			$_SESSION['role'] = $result[0]->getRole();
			$_SESSION['name'] = $result[0]->getName();
			$_SESSION['pass'] = $result[0]->getPass();
	
	
			header('Location: /');
            exit;
		}
		else{
			$error="Неправильно введен логин или пароль!";
		}
	  }
        return $this->render('default/Autorisation.html.twig', array(''
					. 'error' => $error));
    }
	
		 /**
     * @Route("/users", name="users") //good
     */
    public function usersAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])&&$_SESSION['role']==1){
	
	$sucsess="";
	$error="";
	if($request->get('destroy')&&$request->get('iduser')){
$entityManager = $this->getDoctrine()->getManager();
$user_delete = $entityManager->getRepository('AppBundle:Users')->findOneBy(array('id' => $request->get('iduser')));
$entityManager->remove($user_delete);
$entityManager->flush();
header('Location: /users');
	exit;
	}
	$users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
	$login_user="";
	$pass_user="";
	$role_user="";
	$name_user="";
	$phone_user="";
	$adress_user="";
	if($request->get('login')||$request->get('pass')||$request->get('role')||$request->get('name')||$request->get('phone')||$request->get('adress')){
	$login_user=$request->get('login');
	$pass_user=$request->get('pass');
	$role_user=$request->get('role');
	$name_user=$request->get('name');
	$phone_user=$request->get('phone');
	$adress_user=$request->get('adress');
	}
	if($request->get('buttonyes')&&(!$request->get('login')||!$request->get('pass')||!$request->get('role')||!$request->get('name')||!$request->get('phone')||!$request->get('adress'))){
		$error="Не заполненны обязательные поля!";
	}
	if($request->get('login')&&$request->get('pass')&&$request->get('role')&&$request->get('name')&&$request->get('phone')&&$request->get('adress')){
		for($i=0;$i<count($users);$i++){
			if($users[$i]->getName()==$request->get('login')){
				$error="Пользователь с таким логином уже существует!";
			}
			
		}
		$names_cheak=explode(" ",$request->get('name'));
		if(count($names_cheak)!=3){
			$error="Введите правильно - Имя, фамилию и отчество!";
		}
		if($error==""){
		if($request->get('role')=="Менеджер"){
			$role=2;
			
		}
		if($request->get('role')=="Рабочий"){
			$role=3;
			
		}
		
		$string = $request->get('name')."<br> Телефон: ".$request->get('phone')."<br> Адрес: ".$request->get('adress');
		$entityManager = $this->getDoctrine()->getManager();
		$newuser= new Users();
		$newuser->setName($request->get('login'));
        $newuser->setPass(md5($request->get('pass')));
        $newuser->setRole($role);
        $newuser->setInform($string);
        $entityManager->persist($newuser);
        $entityManager->flush();
		$sucsess="Пользователь успешно создан!";
			$login_user="";
	$pass_user="";
	$role_user="";
	$name_user="";
	$phone_user="";
	$adress_user="";
		}
	}
	
	$users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
	for($i=0;$i<count($users);$i++){
		if($users[$i]->getRole() == 1){
			unset($users[$i]);
		}
		
	}
	sort($users);

	return $this->render('default/users.html.twig', array(''
	                . 'login_user' => $login_user, ''
					. 'pass_user' => $pass_user, '' 
	                . 'role_user' => $role_user, ''
					. 'name_user' => $name_user, ''
					. 'phone_user' => $phone_user, '' 
	                . 'adress_user' => $adress_user, ''
					
	                . 'users' => $users, ''
					. 'sucsess' => $sucsess, '' 
	                . 'error' => $error, ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
}
header('Location: /auth');
	exit;
        
    }
		 /**
     * @Route("/skan/hand", name="hand") //good
     */
    public function handAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
	if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
      $names_items = $this->getDoctrine()->getRepository('AppBundle:items')->findAll();
     $sklad = explode(" ",$_SESSION['sklad']);
     $sucsess = "";
	 
$nameproduct="";
$colorproduct="";
$sizeproduct="";
$configproduct="";
$kolproduct="";
if($request->get('nameproduct')&&$request->get('amp;colorproduct')&&$request->get('amp;sizeproduct')&&$request->get('amp;configproduct')&&$request->get('amp;kolproduct')){
	$nameproduct=$request->get('nameproduct');
	$colorproduct=$request->get('amp;colorproduct');
	$sizeproduct=$request->get('amp;sizeproduct');
	$configproduct=$request->get('amp;configproduct');
	$kolproduct=$request->get('amp;kolproduct');
}
//начало завершения сканирования
if($request->get('zaverhit')){
  $entityManager = $this->getDoctrine()->getManager();
$process_delete = $entityManager->getRepository('AppBundle:process')->findOneBy(array('id' => $_SESSION['idtask']));
$taskk = new tasksusers();
        $taskk->setType($process_delete->getTypes());
        $taskk->setName($process_delete->getLotid());
        $taskk->setData(date("d.m.y"));
        $taskk->setNameUser($_SESSION['name']);
        $entityManager->persist($taskk);
        $entityManager->flush();

$entityManager->remove($process_delete);
$entityManager->flush();
unset($_SESSION['idtask']);
unset($_SESSION['sklad']);
unset($_SESSION['sectors_good']);
header('Location: /tasks');
exit;
}
//конец завершения сканирования
$error="";
//начало формирования ячеек для переноса приехавших коробок
if($request->get('name')||$request->get('color')||$request->get('size')||$request->get('config')||$request->get('count')){
if(!$request->get('name')||!$request->get('color')||!$request->get('size')||!$request->get('config')||!$request->get('count')){
	$error = "Незаполненны обязательные поля!";
}
	}
if($request->get('name')&&$request->get('color')&&$request->get('size')&&$request->get('config')&&$request->get('count')){
if(!ctype_digit($request->get('count'))){
	$error = "Некорректно введено количество прибывших коробок";
}



if($error==""){
$perenos = $request->get('count');
   
	$products = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->andWhere('pp.color = :color')
	->andWhere('pp.size = :size')
	->andWhere('pp.config = :config')
    ->andWhere('ep.nameSklad = :sklad')
	->setParameter('name', $request->get('name'))
	->setParameter('color', $request->get('color'))
	->setParameter('size', $request->get('size'))
	->setParameter('config', $request->get('config'))
    ->setParameter('sklad', $sklad[4])
    ->getQuery()
    ->getResult();	
$string = "";
if(!empty($products)){
for($i=0;$i<count($products);$i++){
if($products[$i]['quarty']<$products[$i]['countY']){
$kol_yach = $products[$i]['countY']-$products[$i]['quarty'];

if($perenos>=$kol_yach){
$perenos = $perenos - $kol_yach;
$string = $string."перенести из машины ".$kol_yach." коробок(".$products[$i]['name'].") на сектор ".$products[$i]['nameYacheika']." ";
}
else
{

$string = $string."перенести из машины ".$perenos." коробок(".$products[$i]['name'].") на сектор ".$products[$i]['nameYacheika']." ";
$perenos = 0;
break;
}


}
}
if($perenos!=0){
if(empty($_SESSION['sectors_good'])){
$sectors_good = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findAll();
$sectors_bad = $this->getDoctrine()->getRepository('AppBundle:InventDim')->findAll();
$chet = count($sectors_good);
for($i=0;$i<$chet;$i++){
$sector = explode(".",$sectors_good[$i]->getNameYacheika());

if($sector[0]!=$sklad[4]){
unset($sectors_good[$i]);
}
}
sort($sectors_good);

$chet = count($sectors_good);

for($i=0;$i<$chet;$i++){
for($j=0;$j<count($sectors_bad);$j++){
$a=$sectors_good[$i]->getId();
$b=$sectors_bad[$j]->getWarehouseId();
if($a == $b){
unset($sectors_good[$i]);
break;
}
}
}
sort($sectors_good);//Свободные ячейки на складе, куда приехала машина
$_SESSION['sectors_good']=$sectors_good;
}

 $count_namenklatura =  $this->getDoctrine()->getRepository('AppBundle:items')->findBy(array('name' => $request->get('name')));
$CountY= $count_namenklatura[0]->getCountY();
for($i=0;$i<count($_SESSION['sectors_good']);$i++){
if($perenos>$CountY){
$string = $string."перенести из машины ".$CountY." коробок(".$request->get('name').") на сектор ".$_SESSION['sectors_good'][$i]->getNameYacheika()." ";
$perenos=$perenos-$CountY;
unset($_SESSION['sectors_good'][$i]);

}
else{
$string = $string."перенести из машины ".$perenos." коробок(".$request->get('name').") на сектор ".$_SESSION['sectors_good'][$i]->getNameYacheika()." ";
$perenos=0;
unset($_SESSION['sectors_good'][$i]);

break;
}


}
sort($_SESSION['sectors_good']);

}


}
else
{
if(empty($_SESSION['sectors_good'])){
$sectors_good = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findAll();
$sectors_bad = $this->getDoctrine()->getRepository('AppBundle:InventDim')->findAll();
$chet = count($sectors_good);
for($i=0;$i<$chet;$i++){
$sector = explode(".",$sectors_good[$i]->getNameYacheika());

if($sector[0]!=$sklad[4]){
unset($sectors_good[$i]);
}
}
sort($sectors_good);

$chet = count($sectors_good);

for($i=0;$i<$chet;$i++){
for($j=0;$j<count($sectors_bad);$j++){
$a=$sectors_good[$i]->getId();
$b=$sectors_bad[$j]->getWarehouseId();
if($a == $b){
unset($sectors_good[$i]);
break;
}
}
}
sort($sectors_good);//Свободные ячейки на складе, куда приехала машина
$_SESSION['sectors_good']=$sectors_good;
}

 $count_namenklatura =  $this->getDoctrine()->getRepository('AppBundle:items')->findBy(array('name' => $request->get('name')));
$CountY= $count_namenklatura[0]->getCountY();
for($i=0;$i<count($_SESSION['sectors_good']);$i++){
if($perenos>$CountY){
$string = $string."перенести из машины ".$CountY." коробок(".$request->get('name').") на сектор ".$_SESSION['sectors_good'][$i]->getNameYacheika()." ";
$perenos=$perenos-$CountY;
unset($_SESSION['sectors_good'][$i]);

}
else{
$string = $string."перенести из машины ".$perenos." коробок(".$request->get('name').") на сектор ".$_SESSION['sectors_good'][$i]->getNameYacheika()." ";
$perenos=0;
unset($_SESSION['sectors_good'][$i]);

break;
}


}
sort($_SESSION['sectors_good']);
}
//начало создания таска для приемки

$car = new process();
	 $car->setTypes("Приемка"); 
	 $car->setStatus("Никем не выбрано"); 
	 $car->setColor($request->get('color'));
	 $car->setName($request->get('name'));
	 $car->setLotid($string);
	 $car->setSize($request->get('size'));
	 $car->setConfig($request->get('config')); 
     $car->setWorker(" ");
	 $em = $this->getDoctrine()->getManager();
     $em->persist($car);
     $em->flush();

//конец создания таска для приемки
$sucsess = "Задание успешно добавлено!";
}
}
  
//конец формирования ячеек для переноса приехавших коробок

        return $this->render('default/skanhand.html.twig', array(''
                    . 'sucsess' => $sucsess, ''
					. 'error' => $error, ''
                    . 'nameproduct' => $nameproduct, ''
					. 'colorproduct' => $colorproduct, ''
					. 'sizeproduct' => $sizeproduct, ''
					. 'configproduct' => $configproduct, ''
					. 'kolproduct' => $kolproduct, ''			
					. 'names_items' => $names_items, ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
    
	}
	header('Location: /auth');
	exit;
	
	}
	
	 /**
     * @Route("/sklad", name="sklad") //good
     */
    public function skladAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}

	if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
		if($_SESSION['role']==3){
				header('Location: /lk/'.$_SESSION['name']);
	            exit;
			}
		//начало получения ячеек с количеством коробок	
		$yacheika = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findAll();
		$sklad_yacheika = $this->getDoctrine()->getRepository('AppBundle:wherehouse')->findAll();
        $products = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->getQuery()
    ->getResult();	
		//конец получения ячеек с количеством коробок
			$products_kol= count($products)-1;

	 //начало сортировки
$names_of_sirio = $this->getDoctrine()->getRepository('AppBundle:items')->findBy(array('type' => "Сырье"));
$names_of_prod = $this->getDoctrine()->getRepository('AppBundle:items')->findBy(array('type' => "Продукция"));

$products_nameprod2=null;
$products_nameprod_config=null;
$products_nameprod_size=null;
if($request->get('nameprod')){
 $products_nameprod = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->setParameter('name', $request->get('nameprod'))
    ->getQuery()
    ->getResult();
$count = count($products_nameprod);
for($i=0;$i<$count;$i++){
      for($j=$i+1;$j<$count;$j++){
if($products_nameprod[$i]['color']==$products_nameprod[$j]['color']&&$products_nameprod[$i]['size']==$products_nameprod[$j]['size']&&$products_nameprod[$i]['config']==$products_nameprod[$j]['config'])
{  
          unset($products_nameprod[$i]);
break;
}
      }
}
sort($products_nameprod); //повторения убраны
	
$products_nameprod2=$products_nameprod;
$count = count($products_nameprod2);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){
if($products_nameprod2[$i]['color']==$products_nameprod2[$j]['color']){
unset($products_nameprod2[$i]);
break;
}
}
}
sort($products_nameprod2);
if($request->get('colorprod')){
$products_nameprod_config=$products_nameprod;
$count = count($products_nameprod_config);
for($i=0;$i<$count;$i++){

if($products_nameprod_config[$i]['color']!=$request->get('colorprod')){
unset($products_nameprod_config[$i]);
}

}
sort($products_nameprod_config);

$count = count($products_nameprod_config);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){
if($products_nameprod_config[$i]['config']==$products_nameprod_config[$j]['config']){
unset($products_nameprod_config[$i]);
break;
}
}
}
sort($products_nameprod_config);

if($request->get('configprod')){
$products_nameprod_size=$products_nameprod;
$count = count($products_nameprod_size);
for($i=0;$i<$count;$i++){

if($products_nameprod_size[$i]['color']!=$request->get('colorprod')||$products_nameprod_size[$i]['config']!=$request->get('configprod')){
unset($products_nameprod_size[$i]);
}


}
sort($products_nameprod_size);
$count = count($products_nameprod_size);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){

if($products_nameprod_size[$i]['size']==$products_nameprod_size[$j]['size']){
unset($products_nameprod_size[$i]);
break;
}

}
}
sort($products_nameprod_size);







}

}

}

//////////////////////////////////////////////////////////////////////////////////

$products_namesirio_config=null;
$products_namesirio2=null;
$products_namesirio_size=null;
if($request->get('namesirio')){
 $products_namesirio = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->setParameter('name', $request->get('namesirio'))
    ->getQuery()
    ->getResult();	
$count = count($products_namesirio);

for($i=0;$i<$count;$i++){
      for($j=$i+1;$j<$count;$j++){
if($products_namesirio[$i]['color']==$products_namesirio[$j]['color']&&$products_namesirio[$i]['size']==$products_namesirio[$j]['size']&&$products_namesirio[$i]['config']==$products_namesirio[$j]['config'])
{  
          unset($products_namesirio[$i]);
break;
}
      }
}
sort($products_namesirio); //повторения убраны

$products_namesirio2=$products_namesirio;
$count = count($products_namesirio2);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){
if($products_namesirio2[$i]['color']==$products_namesirio2[$j]['color']){
unset($products_namesirio2[$i]);
break;
}
}
}
sort($products_namesirio2);


if($request->get('colorsirio')){
$products_namesirio_config=$products_namesirio;
$count = count($products_namesirio_config);

for($i=0;$i<$count;$i++){
if($products_namesirio_config[$i]['color']!=$request->get('colorsirio')){
unset($products_namesirio_config[$i]);
}
}
sort($products_namesirio_config);

$count = count($products_namesirio_config);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){

if($products_namesirio_config[$i]['config']==$products_namesirio_config[$j]['config']){
unset($products_namesirio_config[$i]);
break;
}

}

}
sort($products_namesirio_config);

if($request->get('configsirio')){

$products_namesirio_size=$products_namesirio;
$count = count($products_namesirio_size);
for($i=0;$i<$count;$i++){

if($products_namesirio_size[$i]['color']!=$request->get('colorsirio')||$products_namesirio_size[$i]['config']!=$request->get('configsirio')){
unset($products_namesirio_size[$i]);
}

}
sort($products_namesirio_size);

$count = count($products_namesirio_size);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){

if($products_namesirio_size[$i]['size']==$products_namesirio_size[$j]['size']){
unset($products_namesirio_size[$i]);
break;
}

}

}
sort($products_namesirio_size);




}

}

}
	 //конец сортировки
$error="";
$products_nameprod_result=null;	
if($request->get('but2')){

if(empty($request->get('nameprod'))&&empty($request->get('colorprod'))&&empty($request->get('sizeprod'))&&empty($request->get('configprod'))){
$error="Не выбраны обязательные критерии поиска!";

}
if(!empty($request->get('nameprod'))&&empty($request->get('colorprod'))&&empty($request->get('sizeprod'))&&empty($request->get('configprod'))){
$products_nameprod_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->setParameter('name', $request->get('nameprod'))
    ->getQuery()
    ->getResult();

}
if(!empty($request->get('nameprod'))&&!empty($request->get('colorprod'))&&empty($request->get('sizeprod'))&&empty($request->get('configprod'))){
$products_nameprod_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->andWhere('pp.color = :color')
	->setParameter('color', $request->get('colorprod'))
	->setParameter('name', $request->get('nameprod'))
    ->getQuery()
    ->getResult();

}
if(!empty($request->get('nameprod'))&&!empty($request->get('colorprod'))&&empty($request->get('sizeprod'))&&!empty($request->get('configprod'))){
$products_nameprod_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->andWhere('pp.color = :color')
	->andWhere('pp.config = :config')
	->setParameter('color', $request->get('colorprod'))
	->setParameter('name', $request->get('nameprod'))
	->setParameter('config', $request->get('configprod'))
    ->getQuery()
    ->getResult();

}

if($error==""){
	if(!empty($request->get('nameprod'))&&!empty($request->get('colorprod'))&&!empty($request->get('sizeprod'))&&!empty($request->get('configprod'))){
 $products_nameprod_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
    ->andWhere('pp.color = :color')
    ->andWhere('pp.size = :size')
    ->andWhere('pp.config = :config')
	->setParameter('name', $request->get('nameprod'))
    ->setParameter('color', $request->get('colorprod'))
    ->setParameter('size', $request->get('sizeprod'))
    ->setParameter('config', $request->get('configprod'))
    ->getQuery()
    ->getResult();
	}
}

}
$products_namesirio_result=null;
 if($request->get('but')){

if(empty($request->get('namesirio'))&&empty($request->get('colorsirio'))&&empty($request->get('sizesirio'))&&empty($request->get('configsirio'))){
$error="Не выбраны обязательные критерии поиска!";

}
if(!empty($request->get('namesirio'))&&empty($request->get('colorsirio'))&&empty($request->get('sizesirio'))&&empty($request->get('configsirio'))){
$products_namesirio_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->setParameter('name', $request->get('namesirio'))
    ->getQuery()
    ->getResult();

}
if(!empty($request->get('namesirio'))&&!empty($request->get('colorsirio'))&&empty($request->get('sizesirio'))&&empty($request->get('configsirio'))){
$products_namesirio_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->andWhere('pp.color = :color')
	->setParameter('name', $request->get('namesirio'))
	->setParameter('color', $request->get('colorsirio'))
    ->getQuery()
    ->getResult();

}
if(!empty($request->get('namesirio'))&&!empty($request->get('colorsirio'))&&empty($request->get('sizesirio'))&&!empty($request->get('configsirio'))){
$products_namesirio_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->andWhere('pp.color = :color')
	 ->andWhere('pp.config = :config')
	->setParameter('name', $request->get('namesirio'))
	->setParameter('color', $request->get('colorsirio'))
	->setParameter('config', $request->get('configsirio'))
    ->getQuery()
    ->getResult();

}
if($error==""){
	if(!empty($request->get('namesirio'))&&!empty($request->get('colorsirio'))&&!empty($request->get('sizesirio'))&&!empty($request->get('configsirio'))){
 $products_namesirio_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
    ->andWhere('pp.color = :color')
    ->andWhere('pp.size = :size')
    ->andWhere('pp.config = :config')
	->setParameter('name', $request->get('namesirio'))
    ->setParameter('color', $request->get('colorsirio'))
    ->setParameter('size', $request->get('sizesirio'))
    ->setParameter('config', $request->get('configsirio'))
    ->getQuery()
    ->getResult();
	}
}

}  


        return $this->render('default/sklad.html.twig', array(''
                    . 'products_namesirio_result' =>$products_namesirio_result, ''
                    . 'products_nameprod_result' =>$products_nameprod_result, ''
                    . 'products_namesirio_size' =>$products_namesirio_size, ''
                    . 'products_nameprod_size' =>$products_nameprod_size, ''
                    . 'products_nameprod_config' =>$products_nameprod_config, ''
                    . 'products_namesirio_config' =>$products_namesirio_config, ''
                    . 'products_nameprod2' =>$products_nameprod2, ''
                    . 'products_namesirio2' =>$products_namesirio2, ''
                    . 'nameprod' =>$request->get('nameprod'), ''
                    . 'namesirio' =>$request->get('namesirio'), ''
                    . 'colorsirio' =>$request->get('colorsirio'), ''
                    . 'configprod' =>$request->get('configprod'), ''
                    . 'configsirio' =>$request->get('configsirio'), ''
                    . 'colorprod' =>$request->get('colorprod'), ''
                    . 'sizesirio' =>$request->get('sizesirio'), ''
                    . 'sizeprod' =>$request->get('sizeprod'), ''
		            . 'names_of_sirio' =>$names_of_sirio, ''
	             	. 'names_of_prod' =>$names_of_prod, ''
		            . 'yacheika' =>$yacheika, ''
	             	. 'products' =>$products, ''
					. 'sklad_yacheika' =>$sklad_yacheika, ''
					. 'products_kol' =>$products_kol, ''
                    . 'error' =>$error, ''
					. 'color' =>$request->get('color'), ''
					. 'size' =>$request->get('size'), ''
					. 'config' =>$request->get('config'), ''			
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
	}
	header('Location: /auth');
	exit;
    }
	
	 /**
     * @Route("/yacheika/{id}", name="yacheika") //good
     */
    public function yacheikaAction(Request $request,$id)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
		if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
			if($_SESSION['role']==3){
				header('Location: /lk/'.$_SESSION['name']);
	            exit;
			}
		$product = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('dp.nameYacheika = :nameYacheika')
	->setParameter('nameYacheika', $id)
    ->getQuery()
    ->getResult();

//Начало подсчета суммы для графика
$summa=0;	
$sklad=0;
  if(!empty($product)){    
        $sklad = $product[0]['nameSklad'];  
		$products = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
    ->andWhere('pp.color = :color')
    ->andWhere('pp.size = :size')
    ->andWhere('pp.config = :config')
	->setParameter('name', $product[0]['name'])
    ->setParameter('color', $product[0]['color'])
    ->setParameter('size', $product[0]['size'])
    ->setParameter('config', $product[0]['config'])
    ->getQuery()
    ->getResult();	




    for($i=0;$i<count($products);$i++){
$summa=$summa+$products[$i]['quarty'];
}
$summa=$summa-$product[0]['quarty'];
}
//Конец подсчета суммы для графика
	
//Начало внутрескладское перемещение
$error = "";
$asecc = "";
$aa="";
$bb[1]="";
if($request->get('yacheikaout')&&$request->get('kol')&&$request->get('yacheika')){
$aa=$request->get('kol');
$bb=explode(".",$request->get('yacheika'));
if(empty($bb[1])){
$bb[1]="";
}
if($request->get('kol')>$product[0]['quarty']){
$error = "В секторе ".$id." недостаточное количество коробок;";
}

if(!ctype_digit($request->get('kol'))){
$error = "Некорректно введено количество коробок!";
}

$yacheiks = $this->getDoctrine()->getRepository('AppBundle:yacheika')->findAll();
for($i=0;$i<count($yacheiks);$i++){
$a = explode(".",$yacheiks[$i]->getNameYacheika());
if($a[0]!=$product[0]['nameSklad']){
continue;
}
else{
$result_yach[$i]=$yacheiks[$i]->getNameYacheika();
}
}
sort($result_yach);
$flag = 0;

for($i=0;$i<count($result_yach);$i++){
if($result_yach[$i]==$request->get('yacheika')){
$flag = 1;
break;
}
}

if($flag == 0){
$error = "Некорректно указан сектор для отправки.";
}
else{
$productB = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('dp.nameYacheika = :nameYacheika')
	->setParameter('nameYacheika', $request->get('yacheika'))
    ->getQuery()
    ->getResult();
}

if($request->get('yacheika')==$id){
$error = "Данные коробки и так находятся в секторе, который вы указали!";
}


if(!empty($productB)){
if(($productB[0]['countY']-$productB[0]['quarty'])<$request->get('kol')){
$error = "Недостаточно места на секторе ".$request->get('yacheika');
}

if($productB[0]['name']!=$product[0]['name']||$productB[0]['color']!=$product[0]['color']||$productB[0]['size']!=$product[0]['size']||$productB[0]['config']!=$product[0]['config']){

$error = "Перемещение не возможно на сектор ".$request->get('yacheika');
}


}


if($error==""){//Вслучае если ошибок нет
$task = "Перенести из ".$request->get('yacheikaout')." в ".$request->get('yacheika')." ".$request->get('kol')." коробок.";
 $car = new process();
	 $car->setTypes("Внутрескладское перемещение"); 
	 $car->setStatus("Никем не выбрано"); 
	 $car->setColor($product[0]['color']);
	 $car->setName($product[0]['name']);
	 $car->setLotid($task);
	 $car->setSize($product[0]['size']);
	 $car->setConfig($product[0]['config']); 
     $car->setWorker(" ");
	 $em = $this->getDoctrine()->getManager();
     $em->persist($car);
     $em->flush();
$asecc = "Задание успешно добавлено!";
}


}
//Конец внутрескладское перемещение
        return $this->render('default/yacheika.html.twig', array(''
                    . 'asecc' =>$asecc,''
                    . 'name' =>$id,''
                    . 'error' =>$error,''
                    . 'sklad' =>$sklad,''
                    . 'kolichestvo' =>$aa,''
                    . 'nameyach' =>$bb[1],''
                    . 'product' => $product, ''
                    . 'summa' => $summa, ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
					}
	header('Location: /auth');
	exit;
    }
	
	 /**
     * @Route("/yprav", name="yprav")
     */
    public function ypravAction(Request $request)
    {
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
		if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
			if($_SESSION['role']==3){
				header('Location: /lk/'.$_SESSION['name']);
	            exit;
			}
$sucsess="";
$error="";
$summa_result=0;
       if($request->get('button')){
if($request->get('sklad1')){
if($request->get('car1')){
	 $car = new process();
	 $car->setTypes("Приехала машина на склад ".$request->get('sklad1')." на сектор <".$request->get('car1').">"); 
	 $car->setStatus("Никем не выбрано"); 
	 $car->setColor(0);
	 $car->setName(0);
	 $car->setLotid("Отсканировать коробки");
	 $car->setSize(0);
	 $car->setConfig(0);
     $car->setWorker(" "); 
	 $em = $this->getDoctrine()->getManager();
     $em->persist($car);
     $em->flush();
     $sucsess="Задание успешно добавлено!";
}
else{
$error = "Вы не выбрали сектор на который приехала машина!";
}
}
else{
$error = "Вы не выбрали склад на который приехала машина!";
}
	   }
$skladsin=null;
$products_namesirio_config=null;
$products_namesirio2=null;
$products_namesirio_size=null;
$names_of_sirio = null;		   
$skladss = $this->getDoctrine()->getRepository('AppBundle:wherehouse')->findAll();
$n=$request->get('namesirio');
$c=$request->get('colorsirio');
$s=$request->get('sizesirio');
$con=$request->get('configsirio');
if($request->get('skladnameout')){
	if(empty($_SESSION['skladname'])){
		$_SESSION['skladname']=$request->get('skladnameout');
	}
	if($_SESSION['skladname']!=$request->get('skladnameout')){
		$n="";
$c="";
$con="";
$s="";
		$_SESSION['skladname']=$request->get('skladnameout');
	}

	$skladsin = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('ep.nameSklad')
    ->from('AppBundle:wherehouse', 'ep')
    ->andWhere('ep.nameSklad != :name')
	->setParameter('name', $request->get('skladnameout'))
    ->getQuery()
    ->getResult();
	
	$count = count($skladsin);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){
if($skladsin[$i]['nameSklad']==$skladsin[$j]['nameSklad']){

unset($skladsin[$i]);
break;
}
}
}
sort($skladsin);


	
	$names_of_sirio = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('ep.nameSklad = :name')
	->setParameter('name', $request->get('skladnameout'))
    ->getQuery()
    ->getResult();	

$count = count($names_of_sirio);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){
if($names_of_sirio[$i]['name']==$names_of_sirio[$j]['name']){

unset($names_of_sirio[$i]);
break;
}
}
}
sort($names_of_sirio);

	   
	   


if($request->get('namesirio')){

 $products_namesirio = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
	->setParameter('name', $request->get('namesirio'))
    ->getQuery()
    ->getResult();	
$count = count($products_namesirio);

for($i=0;$i<$count;$i++){
      for($j=$i+1;$j<$count;$j++){
if($products_namesirio[$i]['color']==$products_namesirio[$j]['color']&&$products_namesirio[$i]['size']==$products_namesirio[$j]['size']&&$products_namesirio[$i]['config']==$products_namesirio[$j]['config'])
{  
          unset($products_namesirio[$i]);
break;
}
      }
}
sort($products_namesirio); //повторения убраны

$products_namesirio2=$products_namesirio;
$count = count($products_namesirio2);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){
if($products_namesirio2[$i]['color']==$products_namesirio2[$j]['color']){
unset($products_namesirio2[$i]);
break;
}
}
}
sort($products_namesirio2);


if($request->get('colorsirio')){
$products_namesirio_config=$products_namesirio;
$count = count($products_namesirio_config);

for($i=0;$i<$count;$i++){
if($products_namesirio_config[$i]['color']!=$request->get('colorsirio')){
unset($products_namesirio_config[$i]);
}
}
sort($products_namesirio_config);

$count = count($products_namesirio_config);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){

if($products_namesirio_config[$i]['config']==$products_namesirio_config[$j]['config']){
unset($products_namesirio_config[$i]);
break;
}

}

}
sort($products_namesirio_config);

if($request->get('configsirio')){

$products_namesirio_size=$products_namesirio;
$count = count($products_namesirio_size);
for($i=0;$i<$count;$i++){

if($products_namesirio_size[$i]['color']!=$request->get('colorsirio')||$products_namesirio_size[$i]['config']!=$request->get('configsirio')){
unset($products_namesirio_size[$i]);
}

}
sort($products_namesirio_size);

$count = count($products_namesirio_size);
for($i=0;$i<$count;$i++){
 for($j=$i+1;$j<$count;$j++){

if($products_namesirio_size[$i]['size']==$products_namesirio_size[$j]['size']){
unset($products_namesirio_size[$i]);
break;
}

}

}
sort($products_namesirio_size);

if($request->get('sizesirio')){
	
$products_nameprod_result = $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
    ->andWhere('cp.name = :name')
    ->andWhere('pp.color = :color')
    ->andWhere('pp.size = :size')
    ->andWhere('pp.config = :config')
	->andWhere('ep.nameSklad = :namesklad')
	->setParameter('namesklad', $request->get('skladnameout'))
	->setParameter('name', $request->get('namesirio'))
    ->setParameter('color', $request->get('colorsirio'))
    ->setParameter('size', $request->get('sizesirio'))
    ->setParameter('config', $request->get('configsirio'))
    ->getQuery()
    ->getResult();	
for($i=0;$i<count($products_nameprod_result);$i++){
	$summa_result = $summa_result + $products_nameprod_result[$i]['quarty'];
	
}	

if(!empty($_SESSION['tasks_for'])){
	for($i=0;$i<count($_SESSION['tasks_for']);$i++){
		if($_SESSION['tasks_for'][$i]['skladout']==$request->get('skladnameout')&&$_SESSION['tasks_for'][$i]['name']==$request->get('namesirio')&&$_SESSION['tasks_for'][$i]['color']==$request->get('colorsirio')&&$_SESSION['tasks_for'][$i]['size']==$request->get('sizesirio')&&$_SESSION['tasks_for'][$i]['config']==$request->get('configsirio')){
			$summa_result=$summa_result-$_SESSION['tasks_for'][$i]['kol'];
		}
		
	}
	
}
	
}
}
}
}		
}      

$sklads_ware=$this->getDoctrine()->getRepository('AppBundle:wherehouse')->findAll();//получения списка складов
$carses=$this->getDoctrine()->getRepository('AppBundle:car')->findAll();//получения списка машин
$task_skin=null;
if($request->get('but')){

if(empty($request->get('namesirio'))||empty($request->get('colorsirio'))||empty($request->get('sizesirio'))||empty($request->get('configsirio'))||empty($request->get('kolichestvo'))||empty($request->get('skladnamein'))||empty($request->get('skladnameout'))){
$error="Не заполнены обязательные поля!";

}
if(!empty($request->get('namesirio'))&&!empty($request->get('colorsirio'))&&!empty($request->get('sizesirio'))&&!empty($request->get('configsirio'))&&!empty($request->get('kolichestvo'))&&!empty($request->get('skladnamein'))&&!empty($request->get('skladnameout'))&&empty($request->get('car2'))){
$error = "Вы не выбрали сектор на который нужно погрузить коробки!";

}

if($request->get('kolichestvo')){
	if($summa_result<$request->get('kolichestvo')){
	$error = "Недостаточно коробок для перемещения.";	
		
	}
	
	
}
if($error==""){
	if(empty($_SESSION['tasks_for'])){
		$_SESSION['tasks_for'][0]['name']=$request->get('namesirio');
		$_SESSION['tasks_for'][0]['color']=$request->get('colorsirio');
		$_SESSION['tasks_for'][0]['size']=$request->get('sizesirio');
		$_SESSION['tasks_for'][0]['config']=$request->get('configsirio');
		$_SESSION['tasks_for'][0]['kol']=$request->get('kolichestvo');
		$_SESSION['tasks_for'][0]['skladin']=$request->get('skladnamein');
		$_SESSION['tasks_for'][0]['skladout']=$request->get('skladnameout');
		$_SESSION['tasks_for'][0]['sector']=$request->get('car2');
		
	}
else{
	$cout=count($_SESSION['tasks_for']);
		$_SESSION['tasks_for'][$cout]['name']=$request->get('namesirio');
		$_SESSION['tasks_for'][$cout]['color']=$request->get('colorsirio');
		$_SESSION['tasks_for'][$cout]['size']=$request->get('sizesirio');
		$_SESSION['tasks_for'][$cout]['config']=$request->get('configsirio');
		$_SESSION['tasks_for'][$cout]['kol']=$request->get('kolichestvo');
		$_SESSION['tasks_for'][$cout]['skladin']=$request->get('skladnamein');
		$_SESSION['tasks_for'][$cout]['skladout']=$request->get('skladnameout');
		$_SESSION['tasks_for'][$cout]['sector']=$request->get('car2');
	
}
	

			header('Location: /yprav');
	exit;
}

}
if(!empty($_SESSION['tasks_for'])){
$task_skin = $_SESSION['tasks_for'];
}

if($request->get('destroy')){
		unset($_SESSION['tasks_for'][$request->get('id')]);
		unset($task_skin[$request->get('id')]);
		sort($_SESSION['tasks_for']);
		sort($task_skin);
		header('Location: /yprav');
	exit;
}

if($request->get('buttontask')){
	if(empty($_SESSION['tasks_for'])){
		
		$error="Не созданы задачи для формирования тасков!";
	}

	if($error==""){
	
		for($i=0;$i<count($_SESSION['tasks_for']);$i++){
			$products_task= $this->getDoctrine()->getEntityManager()
	->createQueryBuilder()
    ->select('p.inventdimId,p.itemid,cp.id,cp.type,cp.name,cp.countY,pp.id,pp.color,pp.size,pp.config,dp.nameYacheika,pp.quarty,ep.nameSklad')
    ->from('AppBundle:onhand', 'p')
    ->innerJoin('AppBundle:InventDim', 'pp', 'with', "p.inventdimId = pp.id")
    ->innerJoin('AppBundle:items', 'cp', 'with', "p.itemid = cp.id")
	->innerJoin('AppBundle:yacheika', 'dp', 'with', "pp.warehouseId = dp.id")
	->innerJoin('AppBundle:wherehouse', 'ep', 'with', "dp.wherehouseId = ep.id")
	->andWhere('ep.nameSklad = :name')
    ->andWhere('cp.name = :name2')
    ->andWhere('pp.color = :color')
    ->andWhere('pp.size = :size')
    ->andWhere('pp.config = :config')
	->setParameter('name', $_SESSION['tasks_for'][$i]['skladout'])
	->setParameter('name2', $_SESSION['tasks_for'][$i]['name'])
    ->setParameter('color', $_SESSION['tasks_for'][$i]['color'])
    ->setParameter('size',  $_SESSION['tasks_for'][$i]['size'])
    ->setParameter('config',  $_SESSION['tasks_for'][$i]['config'])
    ->getQuery()
    ->getResult();	
			
			
		$string[$i]="";
		$products_task=array_reverse($products_task);
		for($j=0;$j<count($products_task);$j++){
			if($products_task[$j]['quarty']>=$_SESSION['tasks_for'][$i]['kol']){
				$string[$i]=$string[$i]." Перенести ".$_SESSION['tasks_for'][$i]['kol']." коробок из ".$products_task[$j]['nameYacheika']." в машину ";
				$_SESSION['tasks_for'][$i]['kol']=0;
			}
			else{
				$string[$i]=$string[$i]." Перенести ".$products_task[$j]['quarty']." коробок из ".$products_task[$j]['nameYacheika']." в машину ";
				$_SESSION['tasks_for'][$i]['kol']=$_SESSION['tasks_for'][$i]['kol']-$products_task[$j]['quarty'];
			}
			if($_SESSION['tasks_for'][$i]['kol']==0){
				break;
			}
			
		}
		
	 $car = new process();
	 $car->setTypes("Отгрузка из склада ".$_SESSION['tasks_for'][$i]['skladout']." на сектор <".$_SESSION['tasks_for'][$i]['sector']."> (Отправка - ".$_SESSION['tasks_for'][$i]['skladin'].")"); 
	 $car->setStatus("Никем не выбрано"); 
	 $car->setColor($_SESSION['tasks_for'][$i]['color']);
	 $car->setName($_SESSION['tasks_for'][$i]['name']);
	 $car->setLotid($string[$i]);
	 $car->setSize($_SESSION['tasks_for'][$i]['size']);
	 $car->setConfig($_SESSION['tasks_for'][$i]['config']);
     $car->setWorker(" "); 
	 $em = $this->getDoctrine()->getManager();
     $em->persist($car);
     $em->flush();
     $sucsess="Таски успешно созданы!";
			
	 	
			
		}
		
		unset($_SESSION['tasks_for']);	
		$task_skin = null;
	}
}
	   return $this->render('default/yprav.html.twig', array(''
              	    . 'summa_result' =>$summa_result, ''
	                . 'skladss' =>$skladss, ''
					. 'tasks_for' =>$task_skin, ''
					. 'skladsin' =>$skladsin, ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name'], ''
                    . 'products_namesirio_size' =>$products_namesirio_size, ''
                    . 'products_namesirio_config' =>$products_namesirio_config, ''
                    . 'products_namesirio2' =>$products_namesirio2, ''
					. 'nameskladvibor' =>$request->get('skladnameout'), ''
                    . 'skladsinvalue' =>$request->get('skladnamein'), ''
					. 'kolichestvo' =>$request->get('kolichestvo'), ''
					. 'namesirio' =>$n, ''
                    . 'colorsirio' =>$c, ''
                    . 'configsirio' =>$con, ''
                    . 'sizesirio' =>$s, ''
		            . 'names_of_sirio' =>$names_of_sirio, ''
					. 'sklads_ware' => $sklads_ware, ''
                    . 'carses' => $carses, ''
                    . 'error' => $error, ''
		            . 'sucsess' => $sucsess));
    }
	header('Location: /auth');
	exit;
	}

/**
     * @Route("/lk/{id}", name="lk") //good
     */
    public function lkAction(Request $request,$id)	    
{
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
      if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
		   

		   if($id!=$_SESSION['name']&&$_SESSION['role']==3){
			header('Location: /lk/'.$_SESSION['name']);
	  exit;
			}
			
             $user_info = $this->getDoctrine()->getRepository('AppBundle:Users')->findBy(array('name' => $id));
			 $tasks = $this->getDoctrine()->getRepository('AppBundle:process')->findBy(array('worker' => $id));
			 $tasks_all = $this->getDoctrine()->getRepository('AppBundle:tasksusers')->findBy(array('nameUser' => $id));
			 $res=0;
			 for($i=0;$i<count($tasks_all);$i++){
			$data=explode(".", $tasks_all[$i]->getData());
				if($data[1] == date("m") && $data[2] == date("y") ){
					$res=$res+1;
				}
				
			}	  
				 $tasks_all=array_reverse($tasks_all); 
			 
			 
			 
        return $this->render('default/lk.html.twig', array(''
		             . 'user_info' => $user_info, ''
					 . 'tasks' => $tasks, ''
					 . 'res' => $res, ''
					 . 'tasks_all' => $tasks_all, ''	
					 . 'tasks_all_count' => count($tasks_all), ''	
                    . 'type' => $request->get('type'), ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
	  }
	  header('Location: /auth');
	  exit;
    }

/**
     * @Route("/document", name="document") //good
     */
    public function documentAction(Request $request)	    
{
		if($request->get('logout')){
			unset($_SESSION['role']);
			unset($_SESSION['name']);
			unset($_SESSION['pass']);
			session_destroy();
			
		}
      if(!empty($_SESSION['role'])&&!empty($_SESSION['name'])&&!empty($_SESSION['pass'])){
if($_SESSION['role']==3){
				header('Location: /lk/'.$_SESSION['name']);
	            exit;
			}
	     if($request->get('task')&&$request->get('complite')&&$request->get('decs')&&$request->get('type')){
			   $entityManager = $this->getDoctrine()->getManager();
			 $task_delete = $entityManager->getRepository('AppBundle:process')->find($request->get('task'));
			 $color = $task_delete->getColor();
			 $size=$task_delete->getSize();
			 $config=$task_delete->getConfig();
			 $name=$task_delete->getName();
			 $sort = explode(" ",$request->get('type'));
			 $out = $sort[9];
			 $kol = explode(" ",$request->get('decs'));
			 $kol_trans = (count($kol)-1)/8;
$summa=0;			 
$hag=0;
for($i=0;$i<$kol_trans;$i++){
$zadanie[$i]['count'] =$kol[2+$hag]; 
$summa=$summa+$zadanie[$i]['count'];
$hag=$hag+8;
}
$src = "/skan/hand?nameproduct=".$name."&colorproduct=".$color."&sizeproduct=".$size."&configproduct=".$config."&kolproduct=".$summa;
		
		 }
	  
	  
        return $this->render('default/document.html.twig', array(''
		            . 'color' => $color, ''
					. 'size' => $size, ''
					. 'config' => $config, ''
					. 'name' => $name, ''
					. 'summa' => $summa, ''
					. 'src' => $src, ''
					. 'role' => $_SESSION['role'], ''
					. 'login' => $_SESSION['name']));
	  }
	  header('Location: /auth');
	  exit;
    }
}	