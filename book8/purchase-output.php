<?php session_start(); ?>
<?php require '../header.php'; ?>
<?php require '../menu.php'; ?>
<?php
//$pdo=new PDO('mysql:host=localhost;dbname=shop;charset=utf8', 
//	'staff', 'password');
include_once '../connect.php';
$purchase_id=1;
foreach ($pdo->query('select max(id) from purchase') as $row) {
	$purchase_id=$row['max(id)']+1;
}
//トランザクション
try{
	$pdo->beginTransaction();
	$sql=$pdo->prepare('insert into purchase values(?,?)');
	$sql->bindValue(1,$purchase_id,PDO::PARAM_INT);
	$sql->bindValue(2,$_SESSION['customer']['id'],PDO::PARAM_INT);
	$sql->execute();
	foreach ($_SESSION['product'] as $product_id=>$product) {
		$sql=$pdo->prepare('insert into purchase_detail values(?,?,?)');
		$sql->bindValue(1,$purchase_id,PDO::PARAM_INT);
		$sql->bindValue(2,$product_id,PDO::PARAM_INT);
		$sql->bindValue(3,$product['count'],PDO::PARAM_INT);
		$sql->execute();
	}
	$pdo->commit();
	echo '購入手続きが完了しました。ありがとうございます。';
	unset($_SESSION['product']);

}catch(Exception $e){
	//ロールバック
	$pdo->rollBack();
	//エラーとメッセージの表示
	die("error :".$e->getMessage());
}
?>
<?php require '../footer.php'; ?>
