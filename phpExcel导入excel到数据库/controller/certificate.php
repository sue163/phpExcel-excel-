<?php  
class ControllerSaleCertificate extends Controller {
	private $error = array();

	public function index() {
		$this->language->load('sale/certificate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/certificate');

		//判斷是否批量導入excel
		if(isset($_FILES) && !empty($_FILES)){
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
				$files=$_FILES['file'];
				//判断文件是否存在
				if(!empty($files['tmp_name'])){
					$fileTypes = substr($files['name'],strpos($files['name'],'.')+1); //截取文件后缀

				    //上传路径
					$path=DIR_IMAGE."uploadExcel";

					//创建上传路径
				    if (!file_exists($path)) {
				        mkdir($path,0777,true);
				    }

				    $path=$path."/importExcel.".$fileTypes;
		   			$res = move_uploaded_file($files['tmp_name'],$path); //开始上传
		   			if($res){
		   				$importPath = "./image/uploadExcel/importExcel.".$fileTypes;
		   				$this->importExcelBegin($path); //导入excel
		   			}
				}
			}
		}
		

		$this->getList(); 
	}

	
	


	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'sale/certificate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		/*if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['code']) < 3) || (utf8_strlen($this->request->post['code']) > 10)) {
			$this->error['code'] = $this->language->get('error_code');
		}

		$coupon_info = $this->model_sale_coupon->getCouponByCode($this->request->post['code']);

		if ($coupon_info) {
			if (!isset($this->request->get['coupon_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} elseif ($coupon_info['coupon_id'] != $this->request->get['coupon_id'])  {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}*/

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'sale/coupon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function history() {
		$this->language->load('sale/coupon');

		$this->load->model('sale/coupon');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_amount'] = $this->language->get('column_amount');
		$this->data['column_date_added'] = $this->language->get('column_date_added');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  

		$this->data['histories'] = array();

		$results = $this->model_sale_coupon->getCouponHistories($this->request->get['coupon_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $result['amount'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$history_total = $this->model_sale_coupon->getTotalCouponHistories($this->request->get['coupon_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10; 
		$pagination->url = $this->url->link('sale/coupon/history', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->template = 'sale/coupon_history.tpl';		

		$this->response->setOutput($this->render());
	}



	protected function validateImport() {
		if (!$this->user->hasPermission('modify', 'sale/certificate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		//允许上传的文件扩展名
		$array_extention_interdite = array( 'xls' , 'xlsx' , 'csv' );
		$files=$_FILES['file'];	
		if(!empty($files['tmp_name'])){ //判断文件是否存在
			$fileTypes = substr($files['name'],strpos($files['name'],'.')+1); //截取文件后缀
			if(!in_array($fileTypes, $array_extention_interdite)){
				$this->error['warning'] = $this->language->get('error_excel_type');
			}
			if($files['size'] > 5000000){ //设置文件大小不超过5MB
				$this->error['warning'] = $this->language->get('error_excel_size');
			}
		}else{
			$this->error['warning'] = $this->language->get('error_excel');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}


	//批量導入excel數據
	public function importExcel() {
		$this->language->load('sale/certificate');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/certificate');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateImport()) {
			$files=$_FILES['file'];
			//判断文件是否存在
			if(!empty($files['tmp_name'])){
				$fileTypes = substr($files['name'],strpos($files['name'],'.')+1); //截取文件后缀

			    //上传路径
				$path=DIR_IMAGE."uploadExcel";

				//创建上传路径
			    if (!file_exists($path)) {
			        mkdir($path,0777,true);
			    }

			    $path=$path."/importExcel.".$fileTypes;
	   			$res = move_uploaded_file($files['tmp_name'],$path); //开始上传
	   			if($res){
	   				$importPath = "./image/uploadExcel/importExcel.".$fileTypes;
	   				$this->importExcelBegin($path); //导入excel
	   			}
			}
		}
		$this->getList(); 
	}

	//批量導入excel數據
	function importExcelBegin($path){
		$this->load->model('sale/certificate');
		header("Content-Type:text/html;charset=utf-8");
		include_once('../phpExcel/PHPExcel.php');
		include_once('../phpExcel/PHPExcel/IOFactory.php');
		include_once('../phpExcel/PHPExcel/Reader/Excel5.php');

		if(empty($path) OR !file_exists($path)) {
			echo "<script>alert('找不到上传的文件');</script>";
		}

		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if ($extension =='xlsx') {
		    $objReader = new \PHPExcel_Reader_Excel2007();
		    $objPHPExcel = $objReader ->load($path);
		} else if ($extension =='xls') {
		    $objReader = new \PHPExcel_Reader_Excel5();
		    $objPHPExcel = $objReader ->load($path);
		} else if ($extension=='csv') {
		    $PHPReader = new \PHPExcel_Reader_CSV();		    
		    $PHPReader->setInputEncoding('GBK'); //默认输入字符集		    
		    $PHPReader->setDelimiter(','); //默认的分隔符		    
		    $objPHPExcel = $PHPReader->load($path);
		}

	  	$sheet = $objPHPExcel->getSheet(0); 
	  	$highestRow = $sheet->getHighestRow(); // 取得总行数 
	  	// $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	   
	  	//循环读取excel表格,读取一条,插入一条
	  	//j表示从哪一行开始读取 从第二行开始读取，因为第一行是标题不保存
	  	//$a表示列号
	 	for($j=2;$j<=$highestRow;$j++) 
	  	{
		    $data['certificate_number'] = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();
		    $data['license_number']     = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();
		    $data['name']               = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();
		    $data['certificate_type']   = $objPHPExcel->getActiveSheet()->getCell("E".$j)->getValue();
		    $data['license_type']       = $objPHPExcel->getActiveSheet()->getCell("F".$j)->getValue();
		    $data['apply_date']         = date('Y/m/d',PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("G".$j)->getValue()));

		    $data['apply_status']       = $objPHPExcel->getActiveSheet()->getCell("H".$j)->getValue();
		    $data['apply_institution']      = $objPHPExcel->getActiveSheet()->getCell("I".$j)->getValue();
		    $data['center_number']      = $objPHPExcel->getActiveSheet()->getCell("J".$j)->getValue();
		    $data['date_add']           = date("Y-m-d H:i：s",time());
	
			$res = $this->model_sale_certificate->addCertificateImport($data);
	  	}
		unlink($path); //导入完成，删掉excel文件
	}


}
?>