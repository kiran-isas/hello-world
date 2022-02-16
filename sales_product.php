<?php include 'inc_classes.php';?>
<?php include "admin_authentication.php";?>
<?php include "../classes/thumbnail_images.class.php";?>
<?php
if($_REQUEST['record_id'])
{
    $record_id=$_REQUEST['record_id'];
    $sql_record= "SELECT * FROM sales_product where sales_product_id='".$record_id."'";
    if(mysql_num_rows($db->query($sql_record)))
        $row_record=$db->fetch_array($db->query($sql_record));
    else
        $record_id=0;
		
	$sel_map="SELECT disc_type  FROM sales_product_map where sales_product_id='".$record_id."' order by sales_product_id asc limit 0,1";
	$ptr_map=mysql_query($sel_map);
	$data_map=mysql_fetch_array($ptr_map);
	
	$sel_payment_mode1="select payment_mode from payment_mode where payment_mode_id='".$row_record['payment_mode_id']."'";
	$ptr_payment_mode1=mysql_query($sel_payment_mode1);
	$data_payment_mode1=mysql_fetch_array($ptr_payment_mode1);
	$pay_mode=trim($data_payment_mode1['payment_mode']);
	
	$sel_acc_no="select account_no from bank where bank_id='".$row_record['bank_id']."'";
	$ptr_bank_id=mysql_query($sel_acc_no);
	$data_bank_id=mysql_fetch_array($ptr_bank_id);
}

$edit_access='';
$sel_acc="select * from edit_previleges where admin_id='".$_SESSION['admin_id']."' and privilege_id='138'";
$ptr_access=mysql_query($sel_acc);
if(mysql_num_rows($ptr_access))
{
	$edit_access='yes';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php if($record_id) echo "Edit"; else echo "Add";?> Sales Product</title>
<?php include "include/headHeader.php";?>
<?php include "include/functions.php"; ?>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
<!--<script type='text/javascript' src='js/jquery-1.6.2.min.js'></script>-->
    <script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        jQuery(document).ready( function() 
        {
            // binds form submission and fields to the validation engine
            jQuery("#jqueryForm").validationEngine('attach', {promptPosition : "topLeft"});
        });
    </script>
    
	<!-- <script src="../js/jquery.custom/development-bundle/jquery-1.4.2.js"></script>-->
    <link rel="stylesheet" href="js/development-bundle/demos/demos.css"/>
    <link rel="stylesheet" href="js/chosen.css" />
    <script src="js/development-bundle/ui/jquery.ui.core.js"></script>
    <script src="js/development-bundle/ui/jquery.ui.widget.js"></script>
    <script src="js/development-bundle/ui/jquery.ui.datepicker.js"></script>
    <script src="js/chosen.jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
	var pageName = "sales_product";
    $(document).ready(function()  
	{            
		$('.datepicker').datepicker({ changeMonth: true,changeYear: true, showButtonPanel: true, closeText: 'Clear'});
		$.datepicker._generateHTML_Old = $.datepicker._generateHTML; $.datepicker._generateHTML = function(inst)
		{
			res = this._generateHTML_Old(inst); res = res.replace("_hideDatepicker()","_clearDate('#"+inst.id+"')"); return res;
		}
		$("#customer_id").chosen({allow_single_deselect:true});
		//$("#employee_id").chosen({allow_single_deselect:true});
		$("#realtxt").chosen({allow_single_deselect:true});
		<?php
		if($_SESSION['type']=="S" || $_SESSION['type']=='Z' || $_SESSION['type']=='LD' )
		{?>
			$("#branch_name").chosen({allow_single_deselect:true});
			$("#user").chosen({allow_single_deselect:true});
			$("#realtxt").chosen({allow_single_deselect:true});
			$("#customer_id").chosen({allow_single_deselect:true});
		<?php
		}
		?>
	});
	
	$(document).keypress(
		function(event){
		 if (event.which == '13') {
			event.preventDefault();
		  }
	});
    </script>
    
<script language="javascript" src="js/script.js"></script>
<script language="javascript" src="js/conditions-script.js"></script>
<script>
function payment(value)
{
	payment_mode=value.split("-")
	//alert(payment_mode[0]);
	var branch_name=document.getElementById("branch_name").value;
	if(payment_mode[0]=="cheque")
	{
		document.getElementById("chaque_details").style.display = 'block';
		document.getElementById("bank_details").style.display = 'block';
		document.getElementById("credit_details").style.display = 'none';
		document.getElementById("bank_ref_no").style.display = 'none';
		show_bank_for_payment_mode(branch_name,"cheque")
	}
	else if(payment_mode[0]=="Credit Card")
	{
		document.getElementById("chaque_details").style.display = 'none';
		document.getElementById("bank_details").style.display = 'block';
		document.getElementById("credit_details").style.display = 'block';
		document.getElementById("bank_ref_no").style.display = 'none';
		show_bank_for_payment_mode(branch_name,"credit_card")
		
	}
	else if(payment_mode[0]=="paytm")
	{
		document.getElementById("chaque_details").style.display = 'none';
		document.getElementById("bank_details").style.display = 'block';
		document.getElementById("credit_details").style.display = 'none';
		document.getElementById("bank_ref_no").style.display = 'none';
		show_bank_for_payment_mode(branch_name,"paytm")
	}
	else if(payment_mode[0]=="online")
	{
		document.getElementById("bank_ref_no").style.display = 'block';
		document.getElementById("chaque_details").style.display = 'none';
		document.getElementById("bank_details").style.display = 'block';
		document.getElementById("credit_details").style.display = 'none';
		show_bank_for_payment_mode(branch_name,"online")
	}
	else if(payment_mode[0]=="cash")
	{
		document.getElementById("chaque_details").style.display = 'none';
		document.getElementById("bank_details").style.display = 'none';
		document.getElementById("credit_details").style.display = 'none';
		document.getElementById("bank_ref_no").style.display = 'none';
		show_bank_for_payment_mode(branch_name,"")
	}
	else
	{
		document.getElementById("chaque_details").style.display = 'none';
		document.getElementById("bank_details").style.display = 'none';
		document.getElementById("credit_details").style.display = 'none';
		document.getElementById("bank_ref_no").style.display = 'none';
		show_bank_for_payment_mode(branch_name,"")
	}
}

function show_data(id)
{
	//alert(bank_id);
	var branch_name=document.getElementById("branch_name").value;
	var record_id= document.getElementById('record_id').value;
	var data1="action=show_data&action_page=sales_product&id="+id+'&record_id='+record_id+'&branch_name='+branch_name;
	document.getElementById("billing_address").value= '';
	document.getElementById("delivery_address").value= '';
	$.ajax({
	url: "ajax.php", type: "post", data: data1, cache: false,
	success: function (html)
	{
		$('#show_type').html(html);
		// document.getElementById('show_type').value=html;
		$("#realtxt").chosen({allow_single_deselect:true});
		$("#customer_id").chosen({allow_single_deselect:true});
		
	}
	});
}
function show_acc_no(bank_id)
{
	//alert(bank_id);
	var data1="action=show_account&bank_id="+bank_id;
	//alert(data1);
	$.ajax({
	url: "ajax.php", type: "post", data: data1, cache: false,
	success: function (html)
	{
		document.getElementById('account_no').value=html;
	}
	});
}

function show_product_qty(product_id,id)
{
	show_product_desc(id);
	//alert(product_id);
	var prod_data="action=show_product_estimation&product_id="+product_id;
	$.ajax({
		url:"ajax.php",type:"post",timeout: 5000,data:prod_data,cache:false,
		success: function(prod_data)
		{
			prod_qty=prod_data.split("-");
			//alert(prod_data);
			if(prod_qty[0].trim() !='' && prod_qty[1].trim()!='')
			{
				prod_data=prod_qty[0].trim();
				product_price=prod_qty[1].trim();
				product_cgst=prod_qty[2].trim();
				product_sgst=prod_qty[3].trim();
				product_igst=prod_qty[4].trim();
				product_servicetax=prod_qty[5].trim();
			}
			else
			{
				prod_data=0
				product_price=0;
				product_cgst=0;
				product_sgst=0;
				product_igst=0;
				product_servicetax=0;
			}
			
			//base_price=0;
			document.getElementById("base_price"+id).value=0;
			
			if(product_cgst > 0 || product_sgst > 0)
			{
				//base_price=1;
				document.getElementById("base_price"+id).value=1;
				document.getElementById("prod_base_price"+id).value=product_price;
				document.getElementById("prod_price"+id).value=0;
				document.getElementById("prod_price"+id).style.backgroundColor="#cccc";
				document.getElementById("prod_base_price"+id).style.backgroundColor="white";
				document.getElementById("prod_price"+id).readOnly = true;
				document.getElementById("prod_base_price"+id).readOnly = false;
			}
			else
			{
				//base_price=0;
				document.getElementById("base_price"+id).value=0;
				document.getElementById("prod_base_price"+id).value=0;
				document.getElementById("prod_price"+id).value=product_price;
				document.getElementById("mrp_price"+id).value=product_price;
				document.getElementById("prod_base_price"+id).style.backgroundColor="#cccc";
				document.getElementById("prod_price"+id).style.backgroundColor="white";
				document.getElementById("prod_price"+id).readOnly = false;
				document.getElementById("prod_base_price"+id).readOnly = true;			
			}
			//document.getElementById("prod_price"+id).value=product_price;
			document.getElementById("product_total_qty"+id).readOnly = true;
			document.getElementById("product_total_qty"+id).style.backgroundColor="#cccc";
			
			document.getElementById("sales_product_price"+id).value=product_price;
			document.getElementById("product_total_qty"+id).value=prod_data;
			var exit_disc=document.getElementById("product_disc"+id).value = 0;
			if(product_cgst > 0 || product_sgst > 0)
			{
				var exit_cgst=document.getElementById("sin_product_cgst"+id).value = product_cgst;
				var exit_sgst=document.getElementById("sin_product_sgst"+id).value = product_sgst;
			}
			else if(product_igst > 0 )
			{
				var exit_igst=document.getElementById("sin_product_igst"+id).value = product_igst;
			}
			else if(product_servicetax > 0 )
			{
				var exit_igst=document.getElementById("sin_product_vat"+id).value = product_servicetax;	
			}
			
			var exit_servicetax=document.getElementById("product_qty"+id).value = 1;
			
			<?php
			if($record_id=='') { ?>
					var exit_main_discount=document.getElementById("discount").value=0;
					
					var exit_payable_amt=document.getElementById("payable_amount").value=0;
				<?php } ?>
			setTimeout(calc_product_price(id),500);
			setTimeout(showUser,1000);
		}
		});
		
		/*setTimeout(getDiscount(id),600);
		setTimeout(calculte_total_cost,800);
		setTimeout(cal_remaining_amt,900);*/
}
function show_product_desc(ids)
{
	product_id=document.getElementById("product_id"+ids).value;
	//alert(product_id)
	var data1="product_id="+product_id;
	$.ajax({
		url: "get-product_qty.php", type: "post", data: data1, cache: false,
		success: function (html)
		{
			var new_values=html;
			var fields = new_values.split(/-/);
			var unit11 = fields[0];
			var quantity = fields[1];
			
			var sep_field= unit11.split(" ");
			var unit1=sep_field[0];
			var measure=sep_field[1];
			
			var quantity_new = quantity /*- quantity_minus*/;
			
			$("#unit_desc"+ids).html(unit1);
			$("#measure_desc"+ids).html(measure);
			//$("#measure_unit"+ids).val(measure);
			var prod_desc=fields[2];
			var details = prod_desc.split(/#/);
			var price= details[0];
			$("#price_desc"+ids).html(price);
			var brand= details[1];
			var description= details[2];
			document.getElementById("product_details"+ids).style.display="block";
			$("#brand"+ids).html(brand);
			$("#description_desc"+ids).html(description);
				
			if(document.getElementById("select_from"))
			{
				values=document.getElementById("select_from").value;
				show_product_qty(values)
			}
		}
	});
}
function calc_product_price(prod_id)
{
	//alert(prod_id);
	disc_type='';
	frm = document.jqueryForm;  
	disc_type =frm.discount1.value;
	base_price=document.getElementById("base_price"+prod_id).value;
	if(base_price=='1')
	{
		prod_price=document.getElementById("prod_base_price"+prod_id).value;
	}
	else
	{
		prod_price=document.getElementById("prod_price"+prod_id).value;
	//===================================Calculate Base Price============================== Changes 18/1/18
	<?php
	if($_SESSION['tax_type']=='GST')
	{
		?>
		var k=0;
		cgsttax=0;
		cgst_percent=parseFloat(document.getElementById("sin_product_cgst"+prod_id).value);
		if(cgst_percent >0)
		{
			var cgsttax=cgst_percent;
			var k =k+1;
			document.getElementById("sin_product_igst"+prod_id).readOnly = true; 
			document.getElementById("sin_prod_igst_price"+prod_id).readOnly = true; 
			
		}
		else
		{
			document.getElementById("sin_product_igst"+prod_id).readOnly = false; 
			document.getElementById("sin_prod_igst_price"+prod_id).readOnly = false; 	
		}
		sgsttax=0;
		sgst_percent=parseFloat(document.getElementById("sin_product_sgst"+prod_id).value);
		if(sgst_percent >0)
		{
			var sgsttax=sgst_percent;
			var k =k+1;
			document.getElementById("sin_product_igst"+prod_id).readOnly = true; 
			document.getElementById("sin_prod_igst_price"+prod_id).readOnly = true; 
			
		}
		else
		{
			document.getElementById("sin_product_igst"+prod_id).readOnly = false; 
			document.getElementById("sin_prod_igst_price"+prod_id).readOnly = false; 	
		}
		igsttax=0;
		igst_percent=parseFloat(document.getElementById("sin_product_igst"+prod_id).value);
		if(igst_percent >0)
		{
			var igsttax=igst_percent;
			k =k+1;
			document.getElementById("sin_product_cgst"+prod_id).readOnly = true; 
			document.getElementById("sin_prod_cgst_price"+prod_id).readOnly = true; 
			document.getElementById("sin_product_sgst"+prod_id).readOnly = true; 
			document.getElementById("sin_prod_sgst_price"+prod_id).readOnly = true; 
			
		}
		else
		{
			document.getElementById("sin_product_cgst"+prod_id).readOnly = false; 
			document.getElementById("sin_prod_cgst_price"+prod_id).readOnly = false; 	
			document.getElementById("sin_product_sgst"+prod_id).readOnly = false; 
			document.getElementById("sin_prod_sgst_price"+prod_id).readOnly = false; 	
		}
		total_gst=0;
		tot_gst=0;
		tot_base_gst=0;
		if(cgsttax >0 || sgsttax >0)
		{
			var totalgst=Number(cgsttax+sgsttax);
			var new_total_tax=parseFloat(totalgst+100);
			//alert(new_total_tax);
			var tax_new=parseFloat(new_total_tax/100);
			//alert(tax_new);
			//var total_taxable_value = parseFloat(total_price / tax_new);
			//var tot_gst =precisionRound(parseFloat(total_price - total_taxable_value),2);
			//=================================for base price===========================
			var total_base_taxable_value = parseFloat(prod_price / tax_new);
			var tot_base_gst =roundNumber(parseFloat(prod_price - total_base_taxable_value),3);
			//==========================================================================
			total_gst=roundNumber(parseFloat(Number(tot_gst)/k),0);
			//alert(total_gst);
			
			if(cgsttax >0 && (sgsttax <=0 || sgsttax==''))
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=total_gst;
				document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			}
			else if(sgsttax >0 && (cgsttax <=0 || cgsttax==''))
			{
				document.getElementById("sin_prod_sgst_price"+prod_id).value=total_gst;
				document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
			}
			else if(cgsttax >0 && sgsttax >0)
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=total_gst;
				document.getElementById("sin_prod_sgst_price"+prod_id).value=total_gst;
			}
			else
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
				document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			}
			
		}
		else if(igsttax > 0)
		{
			var totalgst=igsttax;
			var new_total_tax=parseFloat((totalgst+100)/100);
			//var total_taxable_value = parseFloat(total_price / new_total_tax);
			//var tot_gst =precisionRound(parseFloat(total_price - total_taxable_value),2);
			//=================================for base price===========================
			var total_base_taxable_value = parseFloat(prod_price / new_total_tax);
			var tot_base_gst =roundNumber(parseFloat(prod_price - total_base_taxable_value),3);
			//==========================================================================
			total_gst=roundNumber(parseFloat(tot_gst/k),3);
			//gst_price=Number(parseFloat(igast_tax)).toFixed(2);
			document.getElementById("sin_prod_igst_price"+prod_id).value=roundNumber(total_base_taxable_value,3);
		}
		else
		{
		}
		<?php
	}
	else
	{
		?>
		servicetax=0;
		service_percent=parseFloat(document.getElementById("sin_product_vat"+prod_id).value);
		if(service_percent >0)
		{
			var servicetax=service_percent;
		}
		total_gst=0;
		tot_gst=0;
		tot_base_gst=0;
		if(servicetax >0)
		{
			var totalgst=Number(servicetax);
			var new_total_tax=parseFloat(totalgst+100);
			//alert(new_total_tax);
			var tax_new=parseFloat(new_total_tax/100);
			//alert(tax_new);
			//var total_taxable_value = parseFloat(total_price / tax_new);
			//var tot_gst =precisionRound(parseFloat(total_price - total_taxable_value),2);
			//=================================for base price===========================
			var total_base_taxable_value = parseFloat(prod_price / tax_new);
			var tot_base_gst =roundNumber(parseFloat(prod_price - total_base_taxable_value),3);
			//==========================================================================
			total_gst=roundNumber(parseFloat(Number(tot_gst)),0);
			//alert(total_gst);
			
			if(servicetax >0)
			{
				document.getElementById("sin_prod_vat_price"+prod_id).value=total_gst;
			}
			else
			{
				document.getElementById("sin_prod_vat_price"+prod_id).value=0;
			}
		}
		<?php
	}
	?>
		//totals_price=Number(total_price) - Number(tot_gst) ;
		prod_price=Number(prod_price) - Number(tot_base_gst) ; //Fpr base price
		totals_base_price=prod_price;
		//total_mrp_price=precisionRound(total_base_price,0);
	//==========================================================End================================18/1/18
	}
	total_prod_price=prod_price;
	total_base_price=prod_price;
	//alert(total_prod_price);
	
	discounted_price=0;
	product_discount=document.getElementById("product_disc"+prod_id).value;
	if(disc_type=="rupees")
	{
		total_prod_price=roundNumber(parseFloat(prod_price-product_discount),3);
		discount_price=roundNumber(parseFloat(product_discount),3);
	}
	else
	{
		discount= parseFloat((prod_price*product_discount)/100);
		total_prod_price=roundNumber(parseFloat(prod_price-discount),3);
		discount_price=roundNumber(parseFloat(discount),3);
	}
	
	document.getElementById("prod_disc_price"+prod_id).value=discount_price;
	document.getElementById("prod_discounted_price"+prod_id).value=total_prod_price;

	prod_qty=document.getElementById("product_qty"+prod_id).value;
	total_prod_qty=document.getElementById("product_total_qty"+prod_id).value;
	if(Number(prod_qty) > Number(total_prod_qty) )
	{
		alert("Issue Quantity is not Greater than Total Quantity");
		document.getElementById("product_qty"+prod_id).value=1;
		prod_qty=1;
	}
	var total_price=total_prod_price * prod_qty;
	document.getElementById("sales_product_price"+prod_id).value=total_price;
	if(base_price==1)
	{
		<?php
		if($_SESSION['tax_type']=='GST')
		{
			?>
			cgst_value=0;
			cgst_base_value=0;
			cgst_percent=parseFloat(document.getElementById("sin_product_cgst"+prod_id).value);
			if(cgst_percent >0)
			{
				cgst_value= roundNumber(parseFloat((total_price*cgst_percent)/100),3);
				cgst_base_value= roundNumber(parseFloat((total_base_price*cgst_percent)/100),3); <!--for base price-->
				cgst_price=roundNumber(parseFloat(cgst_value),3);
				document.getElementById("sin_prod_cgst_price"+prod_id).value=cgst_price;
			}
			else
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
			}
			sgst_value=0;
			sgst_base_value=0;
			sgst_percent=parseFloat(document.getElementById("sin_product_sgst"+prod_id).value);
			if(sgst_percent >0)
			{
				sgst_value= roundNumber(parseFloat((total_price*sgst_percent)/100),3);
				sgst_base_value= roundNumber(parseFloat((total_base_price*sgst_percent)/100),3); <!--for base price-->
				sgst_price=roundNumber(parseFloat(sgst_value),3);
				document.getElementById("sin_prod_sgst_price"+prod_id).value=sgst_price;
			}
			else
			{
				document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			}
			igst_value=0;
			igst_base_value=0;
			igst_percent=parseFloat(document.getElementById("sin_product_igst"+prod_id).value).toFixed(2);
			if(igst_percent >0)
			{
				igst_value= roundNumber(parseFloat((total_price*igst_percent)/100),3);
				igst_base_value= roundNumber(parseFloat((total_base_price*igst_percent)/100),3); <!--for base price-->
				igst_price=roundNumber(parseFloat(igst_value),3);
				document.getElementById("sin_prod_igst_price"+prod_id).value=igst_price;
			}
			else
			{
				document.getElementById("sin_prod_igst_price"+prod_id).value=0;
			}
			
			totals_price=roundNumber(Number(total_price) + Number(cgst_value) + Number(sgst_value)+ Number(igst_value),3);
			total_mrp_price=roundNumber(Number(total_base_price) + Number(cgst_base_value) + Number(sgst_base_value)+ Number(igst_base_value),3);
			//alert(total_mrp_price);
			<?php
		}
		else
		{
			?>
			service_value=0;
			service_base_value=0;
			service_percent=parseFloat(document.getElementById("sin_product_cgst"+prod_id).value);
			if(service_percent >0)
			{
				service_value= roundNumber(parseFloat((total_price*service_percent)/100),3);
				service_base_value= roundNumber(parseFloat((total_base_price*service_percent)/100),3); <!--for base price-->
				service_price=roundNumber(parseFloat(service_value),3);
				document.getElementById("sin_prod_vat_price"+prod_id).value=service_price;
			}
			else
			{
				document.getElementById("sin_prod_vat_price"+prod_id).value=0;
			}
			
			
			totals_price=roundNumber(Number(total_price) + Number(service_value),3);
			total_mrp_price=roundNumber(Number(total_base_price) + Number(service_base_value),3);
			//alert(total_mrp_price);
			<?php
		}
		?>
	}
	else
	{
	//================================CHANGES for BASE PRICE 0==========================================18/1/18
		/* var k=0;
		cgsttax=0;
		cgst_percent=parseFloat(document.getElementById("sin_product_cgst"+prod_id).value);
		if(cgst_percent >0)
		{
			var cgsttax=cgst_percent;
			var k =k+1;
			
		}
		sgsttax=0;
		sgst_percent=parseFloat(document.getElementById("sin_product_sgst"+prod_id).value);
		if(sgst_percent >0)
		{
			var sgsttax=sgst_percent;
			var k =k+1;
		}
		igsttax=0;
		igst_percent=parseFloat(document.getElementById("sin_product_igst"+prod_id).value);
		if(igst_percent >0)
		{
			var igsttax=igst_percent;
			k =k+1;
		}
		total_gst=0;
		tot_gst=0;
		tot_base_gst=0;
		if(cgsttax >0 || sgsttax >0)
		{
			var totalgst=Number(cgsttax+sgsttax);
			var new_total_tax=parseFloat(totalgst+100);
			//alert(new_total_tax);
			var tax_new=parseFloat(new_total_tax/100);
			//alert(tax_new);
			var total_taxable_value = parseFloat(total_price / tax_new);
			//alert(total_taxable_value);
			var tot_gst =precisionRound(parseFloat(total_price - total_taxable_value),2);
			//=================================for base price===========================
			var total_base_taxable_value = parseFloat(total_base_price / tax_new);
			var tot_base_gst =precisionRound(parseFloat(total_base_price - total_base_taxable_value),2);
			//==========================================================================
			total_gst=precisionRound(parseFloat(Number(tot_gst)/k),0);
			alert(total_gst);
			
			if(cgsttax >0 && (sgsttax <=0 || sgsttax==''))
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=total_gst;
				document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			}
			else if(sgsttax >0 && (cgsttax <=0 || cgsttax==''))
			{
				document.getElementById("sin_prod_sgst_price"+prod_id).value=total_gst;
				document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
			}
			else if(cgsttax >0 && sgsttax >0)
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=total_gst;
				document.getElementById("sin_prod_sgst_price"+prod_id).value=total_gst;
			}
			else
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
				document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			}
			
		}
		else if(igsttax > 0)
		{
			var totalgst=igsttax;
			var new_total_tax=parseFloat((totalgst+100)/100);
			var total_taxable_value = parseFloat(total_price / new_total_tax);
			var tot_gst =precisionRound(parseFloat(total_price - total_taxable_value),2);
			//=================================for base price===========================
			var total_base_taxable_value = parseFloat(total_base_price / new_total_tax);
			var tot_base_gst =precisionRound(parseFloat(total_base_price - total_base_taxable_value),2);
			//==========================================================================
			total_gst=precisionRound(parseFloat(tot_gst/k),2);
			//gst_price=Number(parseFloat(igast_tax)).toFixed(2);
			document.getElementById("sin_prod_igst_price"+prod_id).value=precisionRound(total_taxable_value,0);
		}
		else
		{
			document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
			document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			document.getElementById("sin_prod_igst_price"+prod_id).value=0;
		}
		
		totals_price=Number(total_price) - Number(tot_gst) ;
		totals_base_price=Number(total_base_price) - Number(tot_base_gst) ; //Fpr base price
		total_mrp_price=precisionRound(total_base_price,0);
		//alert(total_mrp_price); */
		//=========================================================END CHANGES==========================18/1/18
		<?php
		if($_SESSION['tax_type']=='GST')
		{
			?>
			cgst_value=0;
			cgst_base_value=0;
			cgst_percent=parseFloat(document.getElementById("sin_product_cgst"+prod_id).value);
			if(cgst_percent >0)
			{
				cgst_value= roundNumber(parseFloat((total_price*cgst_percent)/100),3);
				cgst_base_value= roundNumber(parseFloat((total_base_price*cgst_percent)/100),3); <!--fors base price-->
				cgst_price=roundNumber(parseFloat(cgst_value),2);
				//alert(cgst_price);
				document.getElementById("sin_prod_cgst_price"+prod_id).value=cgst_price;
			}
			else
			{
				document.getElementById("sin_prod_cgst_price"+prod_id).value=0;
			}
			sgst_value=0;
			sgst_base_value=0;
			sgst_percent=parseFloat(document.getElementById("sin_product_sgst"+prod_id).value);
			if(sgst_percent >0)
			{
				sgst_value= roundNumber(parseFloat((total_price*sgst_percent)/100),3);
				sgst_base_value= roundNumber(parseFloat((total_base_price*sgst_percent)/100),3); <!--for base price-->
				sgst_price=roundNumber(parseFloat(sgst_value),3);
				//alert(sgst_price);
				document.getElementById("sin_prod_sgst_price"+prod_id).value=sgst_price;
			}
			else
			{
				document.getElementById("sin_prod_sgst_price"+prod_id).value=0;
			}
			igst_value=0;
			igst_base_value=0;
			igst_percent=parseFloat(document.getElementById("sin_product_igst"+prod_id).value);
			if(igst_percent >0)
			{
				igst_value= roundNumber(parseFloat((total_price*igst_percent)/100),3);
				igst_base_value= roundNumber(parseFloat((total_base_price*igst_percent)/100),3); <!--for base price-->
				igst_price=roundNumber(parseFloat(igst_value),3);
				document.getElementById("sin_prod_igst_price"+prod_id).value=igst_price;
			}
			else
			{
				document.getElementById("sin_prod_igst_price"+prod_id).value=0;
			}
			//alert(total_price+"-"+cgst_value+"-"+sgst_value);
			totals_price=roundNumber(Number(total_price) + Number(cgst_value) + Number(sgst_value)+ Number(igst_value),3);
			total_mrp_price=roundNumber(Number(total_base_price) + Number(cgst_base_value) + Number(sgst_base_value)+ Number(igst_base_value),3);
			//alert(totals_price);
		<?php
		}
		else
		{
			?>
			service_value=0;
			service_base_value=0;
			service_percent=parseFloat(document.getElementById("sin_product_vat"+prod_id).value);
			if(service_percent >0)
			{
				service_value= roundNumber(parseFloat((total_price*service_percent)/100),3);
				service_base_value= roundNumber(parseFloat((total_base_price*service_percent)/100),3); <!--fors base price-->
				service_price=roundNumber(parseFloat(service_value),2);
				//alert(cgst_price);
				document.getElementById("sin_prod_vat_price"+prod_id).value=service_price;
			}
			else
			{
				document.getElementById("sin_prod_vat_price"+prod_id).value=0;
			}
			totals_price=roundNumber(Number(total_price) + Number(service_value),3);
			total_mrp_price=roundNumber(Number(total_base_price) + Number(service_base_value),3);
			//alert(totals_price);
			<?php
		}
		?>
	}
	if(base_price==1)
	{
		document.getElementById("prod_price"+prod_id).value=roundNumber(total_mrp_price,3);
		document.getElementById("mrp_price"+prod_id).value=roundNumber(total_mrp_price,3);
		document.getElementById("sales_product_price"+prod_id).value=roundNumber(totals_price,3);
	}
	else
	{
		document.getElementById("sales_product_price"+prod_id).value=roundNumber(totals_price,3);
		document.getElementById("prod_base_price"+prod_id).value=roundNumber(totals_base_price,3);
		document.getElementById("mrp_price"+prod_id).value=roundNumber(total_mrp_price,3);
		//document.getElementById("prod_discounted_price"+prod_id).value=totals_base_price;
	}
	
	
	//totl_price=prod_price_new*(product_discount/100);
	//total_product_price=prod_price_new-totl_price;
	//alert(total_product_price);
	//document.getElementById("sales_product_price"+prod_id).value=total_product_price;
	//}
	//else if(product_discount==0)
	//{
	  ///document.getElementById("sales_product_price"+prod_id).value=prod_price_new;	
	//}
	showUser();
	calculte_total_cost();
	cal_remaining_amt();
}
function show_bank(branch_id,vals)
{
	//alert(branch_id);
	record_id= document.getElementById("record_id").value;
	var bank_data="action=service&show_bnk=1&branch_id="+branch_id+"&payment_type="+vals+"&record_id="+record_id;
	$.ajax({
	url: "show_bank.php",type:"post", data: bank_data,cache: false,
	success: function(retbank)
	{
		document.getElementById("bank_id").innerHTML=retbank;
		if(document.getElementById("bank_name").value)
		{
			//alert(document.getElementById("bank_name").value);
			var bank_ids=document.getElementById("bank_name").value;
			show_acc_no(bank_ids)
		}
	}
	});
	setTimeout(get_product_list(branch_id),300);
	/*var tax_data="show_tax=1&branch_id="+branch_id;
	$.ajax({
	url: "show_tax_type.php",type:"post", data: tax_data,cache: false,
	success: function(rettax)
	{
		document.getElementById("create_type1").innerHTML='';
		document.getElementById("res_tax").value=rettax;
		document.getElementById("type1").value=0;
		
		cal_remaining_amt()
	}
	});*/
}
function get_product_list(branch_id)
{
	var total_product= document.getElementsByName("total_product[]");
	var totals=total_product.length;
	var data1="action=get_sales_product&branch_name="+branch_id+"&totals="+totals;
	$.ajax({
		url: "get_product_list.php", type: "post", data: data1, cache: false,
		success: function (html)
		{
			document.getElementById("create_floor").innerHTML='';
			//document.getElementById("create_type1").innerHTML='';
			document.getElementById("res1").value=html;
		}
	});
	
	var data1="action=get_emp_sales&branch_id="+branch_id;
	$.ajax({
		url: "show_councellor.php", type: "post", data: data1, cache: false,
		success: function (html)
		{
			//document.getElementById("create_floor").innerHTML='';
			//document.getElementById("create_type1").innerHTML='';
			document.getElementById("res2").value=html;
		}
	});
}
function show_bank_for_payment_mode(branch_id,vals)
{
	//alert(branch_id);
	record_id= document.getElementById("record_id").value;
	var bank_data="action=service&show_bnk=1&branch_id="+branch_id+"&payment_type="+vals+"&record_id="+record_id;
	//alert(bank_data)
	$.ajax({
	url: "show_bank.php",type:"post", data: bank_data,cache: false,
	success: function(retbank)
	{
		document.getElementById("bank_id").innerHTML=retbank;
		if(document.getElementById("bank_name").value)
		{
			//alert(document.getElementById("bank_name").value);
			var bank_ids=document.getElementById("bank_name").value;
			show_acc_no(bank_ids)
		}
	}
	});
}
function precisionRound(number, precision) {
  var factor = Math.pow(10, precision);
  return Math.round(number * factor) / factor;
}

</script>
<script>
function showUser()
{
	contact='';
	total_prod_disc_price='';
	var total_sales_product= document.getElementsByName("total_sales_product[]");
	totals=total_sales_product.length;
	//alert(totals);
	contact=''
	for(i=1; i<=totals;i++)
	{
		//alert(i);
		prod_totalssss=parseFloat(Number(document.getElementById("sales_product_price"+i).value));
		prod_disc_price=parseFloat(Number(document.getElementById("prod_discounted_price"+i).value));
		if(prod_disc_price!='')
		{
			total_prod_disc_price=parseFloat(Number(total_prod_disc_price)+Number(prod_disc_price),2);
		}
		//alert("service   "+prod_totalssss);
		if(prod_totalssss!='')
		{
			contact =parseFloat(Number(contact)+Number(prod_totalssss),2);
			//alert(contact);
		}
	}
	//alert(contact);
	document.getElementById('total_prod_discounted_price').value=total_prod_disc_price;
	document.getElementById('product_price').value=contact;
	
	var total_prods_price=contact;
	if(document.getElementById('discount').value)
	{	
		var discount= parseFloat(document.getElementById('discount').value);
	}
	else
	{
		var discount=0; 
	}
	
	frm = document.jqueryForm;  
	discount_type =frm.discount_type.value;
	
	if(discount !=0)
	{
		if(discount_type=="percentage")
		{
			var discount_price= total_prods_price * (discount/100);
		}
		else
		{
			var discount_price= discount;
		}
	}
	else
	{
		var discount_price= 0;
	}
	if(discount_price !=0)
	{
		var total_discount_price= roundNumber(parseFloat(total_prods_price - discount_price),3);
		document.getElementById('discount_price').value=discount_price;
	}
	else
	{
		var total_discount_price=total_prods_price;
		document.getElementById('discount_price').value=discount_price;
	}
	
	document.getElementById('total_price').value=total_discount_price;
	document.getElementById('amount1').value=total_discount_price;
	//calculte_amount_tax();
	/*document.getElementById('total_price').value=contact;
	document.getElementById('amount1').value=contact;*/
	document.getElementById('remaining_amount').value=total_discount_price;
	//calculte_amount_tax(); 25/6/18
	cal_remaining_amt();
}
function getDiscount(idss)
{
	disc_type='';
	frm = document.jqueryForm;  
	disc_type =frm.discount1.value;
	//alert(idss);
	product_price=parseFloat(document.getElementById("prod_price"+idss).value);
	disc=parseFloat(document.getElementById("product_disc"+idss).value);
	//alert(disc)
	
	sin_product_qty=parseFloat(document.getElementById("product_qty"+idss).value);
	//alert(sin_product_qty)
	if(sin_product_qty!='0')
	{
		 total_price_qty=parseFloat(product_price * sin_product_qty);
		// alert('hi')
	}
	else if(sin_product_qty=='0')
	{
		//alert('hi')
		total_price_qty=product_price;
		//alert(total_price_qty)
	}
	//total_price_qty=product_price * sin_product_qty_new;
	//alert(total_price_qty)
	
	if(disc_type=="rupees")
	{
		total_price=roundNumber(parseFloat(total_price_qty-disc),3);
		discount_price=roundNumber(parseFloat(disc),3);
	}
	else
	{
		discount= roundNumber(parseFloat((total_price_qty*disc)/100),3);
		total_price=roundNumber(parseFloat(total_price_qty-discount),3);
		discount_price=roundNumber(parseFloat(discount),3);
	}
	
	document.getElementById("prod_disc_price"+idss).value=discount_price;
	//discount= (total_price_qty*disc)/100;
	//total_price=total_price_qty-discount;
	if(document.getElementById("sales_product_price"+idss))
	{
	  document.getElementById("sales_product_price"+idss).value=total_price;
	}
	showUser();
	calculte_total_cost();
	
}
function calculte_total_cost()
{
	var total_prods_price=document.getElementById("product_price").value;
	
	if(document.getElementById('discount').value)
	{	
		var discount= roundNumber(parseFloat(document.getElementById('discount').value),3);
	}
	else
	{
		var discount=0;
	}
	
	frm = document.jqueryForm;  
	discount_type =frm.discount_type.value;
	
	if(discount !=0)
	{
		if(discount_type=="percentage")
		{
			var discount_price= roundNumber(total_prods_price * (discount/100),3);
		}
		else
		{
			var discount_price= roundNumber(discount,3);
		}
	}
	else
	{
		var discount_price= 0;
	}
	
	if(discount_price !=0)
	{
		var total_cost_new= roundNumber(parseFloat(total_prods_price - discount_price),3);
		document.getElementById('discount_price').value=discount_price;
	}
	else
	{
		var total_cost_new=total_prods_price;
		document.getElementById('discount_price').value=discount_price;
	}
	//var discount=document.getElementById("discount").value;
	//alert(discount)
	//var total=isNaN(parseFloat(product_price * (discount / 100))) ? 0 :parseFloat((product_price * (discount / 100)))
	//alert(total_cost)
	// var total_cost_new=isNaN(parseFloat(product_price - total)) ? 0 :parseFloat(product_price - total)
	//alert(total_cost_new)
	$('#total_price').val(total_cost_new);
	$('#amount1').val(total_cost_new);
	$('#remaining_amount').val(total_cost_new);
	//calculte_amount_tax(); 25/6/18
	cal_remaining_amt();
}
function get_tax_value(val_tax_ids, tax_type)
{
	//alert(tax_type+"-"+val_tax_ids);
	var branch_name=document.getElementById('branch_name').value;
	//alert(branch_name)			
	var data1="tax_type="+tax_type+"&branch_name="+branch_name;	
	//alert(data1);
	$.ajax({
	url: "get_tax_value.php", type: "post", data: data1, cache: false,
	success: function (html)
	{
		//alert("value"+html);
		tax_valuess=html.trim();
		document.getElementById("tax_value"+val_tax_ids).value=tax_valuess;
	}
	});
	
	setTimeout(cal_remaining_amt,800);
}
/*function calculte_amount_tax(val_tax_ids) //25/6/18
{
	tax_value ='';
	var total_tax=document.getElementsByName("total_tax[]").length;
	//alert(val_tax_ids);
	for(i=1;i<=total_tax;i++)
	{
		tax_id='tax_value'+i;
		if(document.getElementById(tax_id))
		{
			tax_value =Number(tax_value) + Number(document.getElementById(tax_id).value);
		}
	}
	//alert(tax_value);
	//tax_value +=document.getElementById('tax_value'+val_tax_ids).value;
    cost_tot_tt=parseFloat(document.getElementById("total_price").value).toFixed(2);//Math.round
	cal_tot_amount=Number(cost_tot_tt * (tax_value/100));
	//alert(cal_tot_amount)
	tot_amount=parseFloat(Number(cost_tot_tt) + Number(cal_tot_amount)).toFixed(2)//Math.round
	//alert(tot_amount)
	//document.getElementById('tax_amount').innerHTML=tot_amount;
	$('#tax_amount'+val_tax_ids).val(cal_tot_amount);

	$('#amount1').val(tot_amount);
	
	cal_remaining_amt();
}*/
</script>
<script>
mail1=Array();
<?php
$sel_sms_cnt="select * from sms_mail_configuration_map where previlege_id='138' ".$_SESSION['where']."";
$ptr_sel_sms=mysql_query($sel_sms_cnt);
$tot_num_rows=mysql_num_rows($ptr_sel_sms);
$i=0;
while($data_sel_cnt=mysql_fetch_array($ptr_sel_sms))
{
	$sel_act="select email from site_setting where admin_id='".$data_sel_cnt['staff_id']."' ".$_SESSION['where']."";
	$ptr_cnt=mysql_query($sel_act);
	if(mysql_num_rows($ptr_cnt))
	{
		$data_cnt=mysql_fetch_array($ptr_cnt);
		?>
		mail1[<?php echo $i; ?>]='<?php echo  $data_cnt['email'];?>';
		<?php
		$i++;
	}
}
if($_SESSION['type']!='S')
{
	$sel_act="select contact_phone,email from site_setting where type='S'";
	$ptr_cnt=mysql_query($sel_act);
	if(mysql_num_rows($ptr_cnt))
	{
		$j=0;
		while($data_cnt=mysql_fetch_array($ptr_cnt))
		{
			?>
			mail1[<?php echo $j; ?>]='<?php echo  $data_cnt['email'];?>';
			<?php
			$j++;
		}
	}
}
"<br/>".$sel_mail_text="select email_text from previleges where privilege_id='138'";
$ptr_mail_text=mysql_query($sel_mail_text);
if($tot_mail_text=mysql_num_rows($ptr_mail_text))
{
	$data_mail_text=mysql_fetch_array($ptr_mail_text);
	?>
	email_text_msg='<?php echo  urlencode($data_mail_text['email_text']);?>';
	<?php
}
?>
//alert(mail1);
function send(ids)
{	
	var sale_product_id=parseInt(ids);
	/*if(document.getElementById('branch_name'))						  
		var branch_name =document.getElementById('branch_name').value;
	else
		var branch_name ='';
	if(document.getElementById('ref_invoice_no'))
		var ref_invoice_no =document.getElementById('ref_invoice_no').value;
	else
		var ref_invoice_no ='';
	if(document.getElementById('realtxt'))
		var realtxt =document.getElementById('realtxt').value;
	else
		var realtxt ='';
	if(document.getElementById('mail'))
		var mail =document.getElementById('mail').value;
	else
		var mail ='';
	if(document.getElementById('user'))
		var user_type =document.getElementById('user').value;
	else
		var user_type ='';
	if(document.getElementById('customer_id'))
		var customer_id =document.getElementById('customer_id').value;
	else
		var customer_id ='';
	if(document.getElementById('no_of_floor'))
		var total_service =document.getElementById('no_of_floor').value;
	else
		var total_service =0;
	concat_string='';
	*/
	/*for(i=1; i<=total_service; i++)
	{
		//product_id = document.getElementById('product_id'+i).value;
		product = document.getElementById('product_id'+i);
		product_id = product.options[product.selectedIndex].text;
		prod_price =document.getElementById('prod_price'+i).value;
		product_qty =document.getElementById('product_qty'+i).value;
		alert("4");
		product_disc =document.getElementById('product_disc'+i).value;
		sales_product_price =document.getElementById('sales_product_price'+i).value;
		concat_string +='&product_id'+i+'='+product_id+'&prod_price'+i+'='+prod_price+'&product_qty'+i+'='+product_qty+'&product_disc'+i+'='+product_disc+'&sales_product_price'+i+'='+sales_product_price;
	}
	
	if(document.getElementById('product_price'))
		var product_price =document.getElementById('product_price').value;
	else
		var product_price ='';
	if(document.getElementById('discount'))
		var discount =document.getElementById('discount').value;
	else
		var discount ='';
	if(document.getElementById('discount_price'))
		var discount_price =document.getElementById('discount_price').value;
	else
		var discount_price ='';
	if(document.getElementById('total_price'))
		var total_price =document.getElementById('total_price').value;
	else
		var total_price ='';
	if(document.getElementById('type1'))
		var type1 =document.getElementById('type1').value;
	else 
		var type1 =0;
	
	concat_string_tax='';
	for(j=1; j<=type1; j++)
	{
		//product_id = document.getElementById('product_id'+i).value;
		tax =document.getElementById('tax_type'+j);
		tax_type = tax.options[tax.selectedIndex].text;
		tax_value =document.getElementById('tax_value'+j).value;
		concat_string_tax +='&tax_type'+j+'='+tax_type+'&tax_value'+j+'='+tax_value;
	}*/
	/*if(document.getElementById('payment_mode'))
	{
		var payment_mode =document.getElementById('payment_mode').value;
		var payment_sep=payment_mode.split("-");
		var payment_mode=payment_sep[0].trim();
	}
	var bank_details="";
	var account_no ="";
	var chaque_details ="";
	var cheque_date ="";
	var credit_details="";
	var credit_card_no ="";
	if(payment_mode !="cash" || payment_mode!='')
	{
		if( bank =document.getElementById('bank_name'))
		{
			var  bank_details=bank.options[bank.selectedIndex].text;
			if(bank_details=="--Select--")
			{
				var bank_details="";
			}
		}
		else
			var bank_details="";
		if(document.getElementById('account_no'))
			var account_no =document.getElementById('account_no').value;
		else
			var account_no ='';
		if(document.getElementById('chaque_no'))
			var chaque_details =document.getElementById('chaque_no').value;
		else
			var chaque_details ='';
		if(document.getElementById('cheque_date'))
			var cheque_date =document.getElementById('cheque_date').value;
		else
			var cheque_date ='';
		//var credit_details =document.getElementById('credit_details').value;
		if(credit_details=="undefined" || credit_details=="")
		{
			credit_details="";
		}
		if(document.getElementById('credit_card_no'))
			var credit_card_no =document.getElementById('credit_card_no').value;
		else
			var credit_card_no ='';
	}
	if(document.getElementById('amount1'))
		var amount1 =document.getElementById('amount1').value;
	else
		var amount1 ='';
	if(document.getElementById('payable_amount'))
		var payable_amount =document.getElementById('payable_amount').value;
	else
		var payable_amount ='';
	if(document.getElementById('remaining_amount'))
		var remaining_amount =document.getElementById('remaining_amount').value;
	else
		var remaining_amount ='';*/
		
	var users_mail=mail1;
	data1='action=sales_product&sale_product_id='+sale_product_id+"&users_mail="+users_mail+"&email_text_msg="+email_text_msg;	
	//alert(data1);				//data1='action=sales_product&branch_name='+branch_name+'&sale_product_id='+sale_product_id+'&ref_invoice_no='+ref_invoice_no+'&realtxt='+realtxt+'&customer_id='+customer_id+'&product_price='+product_price+'&discount='+discount+'&discount_price='+discount_price+'&total_price='+total_price+'&payment_mode='+payment_mode+'&bank_details='+bank_details+'&account_no='+account_no+'&chaque_details='+chaque_details+'&cheque_date='+cheque_date+'&credit_details='+credit_details+'&credit_card_no='+credit_card_no+'&amount1='+amount1+'&payable_amount='+payable_amount+'&remaining_amount='+remaining_amount+concat_string+'&total_service='+total_service+'&type1='+type1+"&users_mail="+users_mail+"&mail="+mail+"&email_text_msg="+email_text_msg+"&user_type="+user_type;
	//alert(data1);
	if(sale_product_id >0)
	{
		$.ajax({
			url:'send_email.php',type:"post",data:data1,cache:false,crossDomain:true, async:false,
			success: function(response) {
			//alert(response);
			return true;
			}
		});
	}
}
</script>
<script>
function show_mobile_no(cust_ids,type)
{
	var data2="customer_id="+cust_ids+"&type="+type;
	//alert(data2);
	 $.ajax({
	url: "get_mail.php", type: "post", data: data2, cache: false,
	success: function (html)
	{
		if(html.trim()!='')
		{
			sep=html.split("###");
			
			var mail= sep[0].trim();
			document.getElementById('mail').value=mail;
			document.getElementById('billing_address').value=sep[1].trim();
			document.getElementById('delivery_address').value=sep[2].trim();
		}
	}
	});
	//alert(cust_ids);
	if(cust_ids == 'custome')
	{
		$( ".new_custom_course" ).dialog({
			width: '500',
			height:'300'
		});
	}
	else if(cust_ids == '18')
	{
		var data2="action_for=Ahmedabad&customer_id="+cust_ids;
		//alert(data2);
		 $.ajax({
		url: "get_stockist.php", type: "post", data: data2, cache: false,
		success: function (html)
		{
			//alert(html);
			document.getElementById('stockiest').innerHTML=html;
		}
		});
		$("#stockist_id").chosen({allow_single_deselect:true});
	}
	else if(cust_ids == '1113')
	{
		var data2="action_for=ISAS PCMC&customer_id="+cust_ids;
		//alert(data2);
		 $.ajax({
		url: "get_stockist.php", type: "post", data: data2, cache: false,
		success: function (html)
		{
			document.getElementById('stockiest').innerHTML=html;
			$("#stockist_id").chosen({allow_single_deselect:true});
		}
		});
		$("#stockist_id").chosen({allow_single_deselect:true});
	}
	else if(cust_ids == '1212')
	{
		var data2="action_for=Ahmednagar&customer_id="+cust_ids;
		//alert(data2);
		 $.ajax({
		url: "get_stockist.php", type: "post", data: data2, cache: false,
		success: function (html)
		{
			document.getElementById('stockiest').innerHTML=html;
			$("#stockist_id").chosen({allow_single_deselect:true});
		}
		});
		$("#stockist_id").chosen({allow_single_deselect:true});
	}
	else if(cust_ids == '1805')
	{
		var data2="action_for=Pune&customer_id="+cust_ids;
		//alert(data2);
		 $.ajax({
		url: "get_stockist.php", type: "post", data: data2, cache: false,
		success: function (html)
		{
			document.getElementById('stockiest').innerHTML=html;
			$("#stockist_id").chosen({allow_single_deselect:true});
		}
		});
		$("#stockist_id").chosen({allow_single_deselect:true});
	}
	else if(cust_ids == '2915')
	{
		var data2="action_for=ISAS Singhgad Road&customer_id="+cust_ids;
		//alert(data2);
		 $.ajax({
		url: "get_stockist.php", type: "post", data: data2, cache: false,
		success: function (html)
		{
			document.getElementById('stockiest').innerHTML=html;
			$("#stockist_id").chosen({allow_single_deselect:true});
		}
		});
		$("#stockist_id").chosen({allow_single_deselect:true});
	}
}
function cal_remaining_amt()
{
	var final_amt=parseFloat(document.getElementById('amount1').value);
	//alert(final_amt);
	
	var payable_amt=parseFloat(document.getElementById('payable_amount').value);
	//alert(payable_amt);
	
	if(payable_amt > final_amt+1)
	{
	  alert("Payable Amount should not be greater than Final amount..");
	  document.getElementById("payable_amount").value=0;	
	  $('#remaining_amount').val(final_amt);
	  return false;
	}
	
	if(payable_amt!=0)
	{
		cal_tot_rem_amt=parseFloat(final_amt - payable_amt);
		
	}
	else
	{
	  cal_tot_rem_amt=final_amt;
	  
	}
	//alert(cal_tot_rem_amt);
	
	$('#remaining_amount').val(cal_tot_rem_amt);
}

function validme()
{
	frm = document.jqueryForm;
	error='';
	disp_error = 'Clear The Following Errors : \n\n';
	 
	if(frm.user.value=='')
	{
		disp_error +='Select User Type\n';
		document.getElementById('user').style.border = '1px solid #f00';
		frm.user.focus();
		error='yes';
	}
	else
	{
		if(frm.customer_id.value=='')
		{
			disp_error +='Select Customer name\n';
			document.getElementById('customer_id').style.border = '1px solid #f00';
			frm.customer_id.focus();
			error='yes';
		}
	}
	 
	 /*var fields = $("input[name='requirment_id[]']").serializeArray(); 
	 if (fields.length == 0) 
	  { 
		disp_error +='Select Product\n';
		 
		error='yes';
	  }*/ 	 
	if(frm.product_price.value=='')
	{
		disp_error +='Enter Product Price\n';
		document.getElementById('product_price').style.border = '1px solid #f00';
		frm.product_price.focus();
		error='yes';
	}
	if(frm.payable_amount.value=='')
	{
		disp_error +='Enter Payble amount\n';
		document.getElementById('payable_amount').style.border = '1px solid #f00';
		frm.payable_amount.focus();
		error='yes';
	}
	if(error=='yes')
	{
		alert(disp_error);
		return false;
	}
	else
	{
		
	}
}
function searchSel(value,type) 
{
	var data1="action=sale_product&mobile_no="+value+"&type="+type;	
	$.ajax({
	url: "get_name.php", type: "post", data: data1, cache: false,
	success: function (html)
	{
		if(html.trim()!='')
		{
			sep=html.split("###");
			/*$( "#customer_id_chosen").toggleClass("chosen-with-drop chosen-container-active");
			$( "#customer_id").find("[data-option-array-index="+html+"]").toggleClass("result-selected");*/
			document.getElementById("sel_cust").innerHTML=sep[1];
			$("#customer_id").chosen({allow_single_deselect:true});
			/*$("#customer_id option").removeAttr("selected");
			$("#customer_id option[value="+html+"]").attr("selected", "selected");
			*/
			//setTimeout(getMembership(sep[0].trim()),500);
			document.getElementById("mail").value=sep[2];
			document.getElementById("billing_address").value=sep[3];
			document.getElementById("delivery_address").value=sep[4];
		}
	}
	});
} 
</script> 

</head>
<body>
<?php include "include/header.php";?>
<!--info start-->
<div id="info">
<!--left start-->
<?php include "include/menuLeft.php"; ?>
<!--left end-->
<!--right start-->
<div id="right_info">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td class="top_left"></td>
    <td class="top_mid" valign="bottom"><?php include "include/product_category_menu.php"; ?></td>
    <td class="top_right"></td>
  </tr>
  <tr>
    <td class="mid_left"></td>
    <td class="mid_mid">
        <table width="100%" cellspacing="0" cellpadding="0">
        <?php
		$errors=array(); $i=0;
		$success=0;
		if($_POST['save_changes'])
		{
			if($_POST['added_date'] !=''){
				$ad_date=explode('/',$_POST['added_date'],3);
				$added_date=$ad_date[2].'-'.$ad_date[1].'-'.$ad_date[0];
			}
			else $added_date=date('Y-m-d');
			//$branch_name=$_POST['branch_name'];
			$branch_name=( ($_POST['branch_name'])) ? $_POST['branch_name'] : "";
			$billing_address=( ($_POST['billing_address'])) ? $_POST['billing_address'] : "";
			$delivery_address=( ($_POST['delivery_address'])) ? $_POST['delivery_address'] : "";
			//$customer_id=$_POST['customer_id'];
			$customer_id=( ($_POST['customer_id'])) ? $_POST['customer_id'] : "0";
			//$product_price=$_POST['product_price']; 
			$product_price=( ($_POST['product_price'])) ? $_POST['product_price'] : "0";
			//$discount=$_POST['discount'];   
			$discount=( ($_POST['discount'])) ? $_POST['discount'] : "0";
			$discount_type=( ($_POST['discount_type'])) ? $_POST['discount_type'] : "";
			$discount_price=( ($_POST['discount_price'])) ? $_POST['discount_price'] : "0";
			//$total_price=$_POST['amount1'];
			$total_price=( ($_POST['amount1'])) ? $_POST['amount1'] : "0";
			//$tot_price_withou_tax=$_POST['total_price'];
			$tot_price_withou_tax=( ($_POST['total_prod_discounted_price'])) ? $_POST['total_prod_discounted_price'] : "0";
			$show_gst=($_POST['show_gst']) ? $_POST['show_gst'] : "";
			//$payable_amount=$_POST['payable_amount'];
			//$total_prod_discounted_price=($_POST['total_prod_discounted_price']) ? $_POST['total_prod_discounted_price'] : "0";
			$payable_amount=( ($_POST['payable_amount'])) ? $_POST['payable_amount'] : "0";
			$remaining_amount=( ($_POST['remaining_amount'])) ? $_POST['remaining_amount'] : "0";
			$type=( ($_POST['user'])) ? $_POST['user'] : "0";
			$bank_name='0';
			$chaque_no='';
			$date='';
			$credit_card_no='';
			$payment_mode_id='0';
			$payment_type_val='0';
			if($_POST['payment_mode'] !='')
			{
				$payment_mode=$_POST['payment_mode'];
				$sep=explode("-",$payment_mode);
				$payment_mode_id=$sep[1];
				$payment_type_val=$sep[0];
			}
			//$ref_invoice_no=$_POST['ref_invoice_no'];
			$ref_invoice_no=( ($_POST['ref_invoice_no'])) ? $_POST['ref_invoice_no'] : "0";
			//$amount=$_POST['amount'];
			$amount=( ($_POST['amount'])) ? $_POST['amount'] : "0";
			if($payment_mode_id !="1")
			{
				$bank_name=( ($_POST['bank_name'])) ? $_POST['bank_name'] : "0";
				$chaque_no=( ($_POST['chaque_no'])) ? $_POST['chaque_no'] : "";
				$credit_card_no=( ($_POST['credit_card_no'])) ? $_POST['credit_card_no'] : "";
				if($_POST['cheque_date'] !='')
				{
					$cheques_date=( ($_POST['cheque_date'])) ? $_POST['cheque_date'] : "";
					$chaquess_date = explode('/',$cheques_date);
					$chaque_date=$chaquess_date[2].'-'.$chaquess_date[1].'-'.$chaquess_date[0];
				}
				else
					$chaque_date='';
			}
			$bank_ref_no=( ($_POST['bank_ref_no'])) ? $_POST['bank_ref_no'] : "0";
			if($_SESSION['type']=='S' || $_SESSION['type']=='Z' || $_SESSION['type']=='LD' )
			{
				$sel_branch="select cm_id from site_setting where branch_name='".$branch_name."' and type='A'";
				$ptr_branch=mysql_query($sel_branch);
				$data_branch=mysql_fetch_array($ptr_branch);
				$cm_id=$data_branch['cm_id'];
				$branch_name1=$branch_name;
				$data_record['cm_id']=$cm_id;
				$cm_id1=$cm_id;
			}	
			else
			{
				$data_record['cm_id']=$_SESSION['cm_id'];
				$branch_name1=$_SESSION['branch_name'];
				$data_record['cm_id']=$_SESSION['cm_id'];
				$cm_id1=$_SESSION['cm_id'];
				$cm_id=$_SESSION['cm_id'];;
			}
			$data_record['admin_id']=$_SESSION['admin_id'];
			if(count($errors))
			{
				?>
				<tr>
					<td> <br></br>
					<table align="left" style="text-align:left;" class="alert">
					<tr><td ><strong>Please correct the following errors</strong><ul>
							<?php
							for($k=0;$k<count($errors);$k++)
									echo '<li style="text-align:left;padding-top:5px;" class="green_head_font">'.$errors[$k].'</li>';?>
							</ul>
					</td></tr>
					</table>
					</td>
				</tr>   <br></br>  
				<?php
			}
			else
			{
				$success=1;
				$data_record['customer_id'] =$customer_id;							
				$data_record['product_price'] =$product_price;
				$data_record['discount'] =$discount;
				$data_record['discount_price'] =$discount_price;
				$data_record['discount_type'] =$discount_type;
				$data_record['total_price']=$total_price;
				$data_record['tot_price_withou_tax']=$tot_price_withou_tax;
				$data_record['payable_amount']=$_POST['payable_amount'];
				$data_record['remaining_amount']=$remaining_amount;
				$data_record['added_date']=$added_date;
				$data_record['show_gst']=$show_gst;
				$data_record['billing_address']=$billing_address;
				$data_record['delivery_address']=$delivery_address;
				$data_record['payment_mode_id'] =$payment_mode_id;
				$data_record['chaque_no'] =$chaque_no;
				$data_record['chaque_date'] =$chaque_date;
				$data_record['credit_card_no'] =$credit_card_no;
				$data_record['bank_ref_no'] =$bank_ref_no;
				$data_record['bank_id'] =$bank_name;
				$total_floor=$_POST['floor'];
				$total_type1=$_POST['total_type1'];
				$data_record['ref_invoice_no']=$ref_invoice_no;
				$data_record['type']=$type;
				if($record_id)
				{
					//echo 'hi';
					/*"<br />".$del_sales_product="delete from sales_product_map where sales_product_id='".$record_id."'";
					$ptr_del_section=mysql_query($del_sales_product);
					$where_record=" sales_product_id='".$record_id."'";
					$db->query_update("sales_product", $data_record,$where_record);
					
					for($i=0;$i<count($product);$i++)
					{
						 $quantity=$_POST['quantity'][$i];
						  'total_qty=> '.$quantity_total=$_POST['quantity_total'][$i];
						   
						 $ins_product="insert into sales_product_map (`sales_product_id`,`product_id`,`quantity`) values ('".$record_id."','".$_POST['requirment_id'][$i]."', '".$quantity."')";
						$ptr_product=mysql_query($ins_product);
						
						 '<br/>'.$select_quantity="select SUM(quantity) from sales_product_map where sales_product_id='".$record_id."'  and                                           product_id='".$_POST['requirment_id'][$i]."'";
						   $query_quantity=mysql_query($select_quantity);
						   if(mysql_num_rows($query_quantity))
						   {
							 $fetch_partial_quantity=mysql_fetch_array($query_quantity);
								  
							  'sum_qty=> '.$partial_complete_qty=$fetch_partial_quantity['SUM(quantity)'];
							
							   'rem=><br/>'.$total_remaining_qty= $quantity_total - $partial_complete_qty; 
						   }
						 '<br/>'.$update_product="update product set quantity='".$total_remaining_qty."' where product_id='".$_POST['requirment_id'][$i]."' ";
						  $query_update=mysql_query($update_product);
					}
					echo '<br></br><div id="msgbox" style="width:40%;">Record updated successfully</center></div><br></br>';*/
					"<br>".$insert="INSERT INTO `log_history`(`category`, `action`, `name`, `id`, `date`, `cm_id`, `admin_id`) VALUES ('sales_product','Edit','sale product','".$record_id."','".date('Y-m-d H:i:s')."','".$_SESSION['cm_id']."','".$_SESSION['admin_id']."')";
					$query=mysql_query($insert);
				}
				else
				{
					//==============Update Invoice No.=====================
					$sel_inv="select ext_invoice_no from sales_product where cm_id='".$data_record['cm_id']."' and ext_invoice_no IS NOT NULL order by ext_invoice_no desc limit 0,1";
					$ptr_inv=mysql_query($sel_inv);
					$data_inv=mysql_fetch_array($ptr_inv);
					
					$recp=explode("/",$data_inv['ext_invoice_no']);
					$inv_no=intval($recp[2])+1;
					$preinv=$recp[0].'/'.$recp[1].'/';
					$data_record['ext_invoice_no']=$preinv.$inv_no;
					//======================================================
					
					$record_id=$db->query_insert("sales_product", $data_record);
					$sales_ins_id=$record_id;// For Manual customer
					for($i=1;$i<=$total_floor;$i++)
					{
						$data_record_service['sales_product_id'] =$record_id; 
						//$data_record_service['product_id'] =$_POST['product_id'.$i];
						$data_record_service['product_id']=( ($_POST['product_id'.$i])) ? $_POST['product_id'.$i] : "0";
						//$data_record_service['prod_price'] =$_POST['prod_price'.$i];
						$data_record_service['prod_price']=( ($_POST['prod_price'.$i])) ? $_POST['prod_price'.$i] : "0";
						$data_record_service['base_prod_price']=( ($_POST['prod_base_price'.$i])) ? $_POST['prod_base_price'.$i] : "0";
						$data_record_service['disc_type']=( ($_POST['discount1'])) ? $_POST['discount1'] : "";
						//$data_record_service['product_qty'] =$_POST['product_qty'.$i];
						$data_record_service['product_qty']=( ($_POST['product_qty'.$i])) ? $_POST['product_qty'.$i] : "0";
						//$data_record_service['product_disc'] =$_POST['product_disc'.$i];
						$data_record_service['discounted_price']=( ($_POST['prod_discounted_price'.$i])) ? $_POST['prod_discounted_price'.$i] : "0";
						$data_record_service['cgst_tax_in_per'] =$_POST['sin_product_cgst'.$i] ? $_POST['sin_product_cgst'.$i] : "0";
						$data_record_service['staff_id'] =$_POST['staff_id'.$i] ? $_POST['staff_id'.$i] : "0";
						$data_record_service['cgst_tax'] =$_POST['sin_prod_cgst_price'.$i] ? $_POST['sin_prod_cgst_price'.$i] : "0";
						$data_record_service['sgst_tax_in_per'] =$_POST['sin_product_sgst'.$i] ? $_POST['sin_product_sgst'.$i] : "0";
						$data_record_service['sgst_tax'] =$_POST['sin_prod_sgst_price'.$i] ? $_POST['sin_prod_sgst_price'.$i] : "0";
						$data_record_service['igst_tax_in_per'] =$_POST['sin_product_igst'.$i] ? $_POST['sin_product_igst'.$i] : "0";
						$data_record_service['igst_tax'] =$_POST['sin_prod_igst_price'.$i] ? $_POST['sin_prod_igst_price'.$i] : "0";
						$data_record_service['service_tax_in_per'] =$_POST['sin_product_vat'.$i] ? $_POST['sin_product_vat'.$i] : "0";
						$data_record_service['service_tax'] =$_POST['sin_prod_vat_price'.$i] ? $_POST['sin_prod_vat_price'.$i] : "0";
						$data_record_service['product_disc']=( ($_POST['product_disc'.$i])) ? $_POST['product_disc'.$i] : "0";
						//$data_record_service['sales_product_price'] =$_POST['sales_product_price'.$i];
						$data_record_service['sales_product_price']=( ($_POST['sales_product_price'.$i])) ? $_POST['sales_product_price'.$i] : "0";
						$customer_service_id=$db->query_insert("sales_product_map", $data_record_service);
						
						"<br/>post- ".$quantity_total=intval($_POST['product_total_qty'.$i]);
						"<br/>".$select_quantity="select SUM(`product_qty`) as qty from `sales_product_map` where `sales_product_id`='".$record_id."' and `product_id`='".$data_record_service['product_id']."'";
						$query_quantity=mysql_query($select_quantity);
						if(mysql_num_rows($query_quantity))
						{
							$fetch_partial_quantity=mysql_fetch_array($query_quantity);
							$partial_complete_qty=intval($fetch_partial_quantity['qty']);
							$total_remaining_qty= intval($quantity_total) - intval($partial_complete_qty); 
							$data_qty_record['quantity']=$total_remaining_qty;
						}
						
						$where_qty_record=" product_id='".$_POST['product_id'.$i]."'";
						$db->query_update("product", $data_qty_record,$where_qty_record);
						
						$sel_stockiest="select admin_id from product where product_id='".$_POST['product_id'.$i]."' ";
						$ptr_stock=mysql_query($sel_stockiest);
						$data_stock=mysql_fetch_array($ptr_stock);
						
						$ins_data="INSERT INTO `product_daily_report`(`product_id`, `cm_id`, `stockiest_id`, `type`, `purchase_qty`, `sales_qty`, `checkout_qty`, `vendor_id`, `cust_type`, `cust_id`, `todays_qty`,`todays_shelf_qty`,`todays_consumable_qty`, `description`,`admin_id`,`added_date`) VALUES ('".$_POST['product_id'.$i]."','".$cm_id."','".$data_stock['admin_id']."','sales','0','".$_POST['product_qty'.$i]."','0','0','".$_POST['user']."','".$customer_id."','product_qty -".$_POST['product_qty'.$i]."','','','Product Sales','".$_SESSION['admin_id']."','".date('Y-m-d H:i:s')."')";
						$ptr_ins=mysql_query($ins_data);
						//echo "<br/>".$qty_update="UPDATE `product` SET `quantity`='".(int)$total_remaining_qty."' WHERE `product_id`='".$data_record_service['product_id']."'";
						//$ptr_qty_update=mysql_query($qty_update) or die(mysql_error());
					}
					
					/*for($j=1;$j<=$total_type1;$j++)//25-6-18
					{
						$data_record_tax['sales_product_id'] =$record_id; 
						$data_record_tax['tax_type'] =$_POST['tax_type'.$j];
						$data_record_tax['tax_value'] =$_POST['tax_value'.$j];
						
						$customer_tax_id=$db->query_insert("sales_product_tax_map", $data_record_tax);
					}*/
					if($remaining_amount>0)
					$status='pending';
					else
					$status='paid';
					//===============Update Receipt no================
					$sel_recpt="select receipt_no from sales_product_invoice where cm_id='".$cm_id1."' and (receipt_no IS NOT NULL and receipt_no !='') order by invoice_id desc limit 0,1";
					$ptr_recpt=mysql_query($sel_recpt);
					$data_receipt=mysql_fetch_array($ptr_recpt);
					
					$recp=explode("-",$data_receipt['receipt_no']);
					$recpt_no=intval($recp[1])+1;
					$pre=$recp[0].'-';
					$receipt_no=$pre.$recpt_no;
					//==================================================
					
					$insert_sales_invoice = "INSERT INTO `sales_product_invoice` (`sales_product_id`,`receipt_no`, `total_price`, `payable_amount`,`remaining_amount`, `paid_type`, `bank_id`, `cheque_detail`, `chaque_date`, `credit_card_no`,`bank_ref_no`, `admin_id`, `added_date`,`status`,`cm_id`,`total_paid`) VALUES ('".$record_id."','".$receipt_no."', '".$total_price."', '".$payable_amount."','".$remaining_amount."', '".$payment_mode_id."','".$bank_name."', '".$chaque_no."', '".$chaque_date."','".$credit_card_no."','".$bank_ref_no."', '".$_SESSION['admin_id']."', '".$added_date."','".$status."','".$cm_id1."','".$payable_amount."'); ";
					$ptr_sales_invoice = mysql_query($insert_sales_invoice);	
					$ins_id=mysql_insert_id();
					
					if($payment_mode_id=='2' || $payment_mode_id=='4' || $payment_mode_id=='5')
					{
						$bank="INSERT INTO `bank_records`(`bank_id`, `type`, `record_id`, `invoice_id`, `amount`, `added_date`, `cm_id`, `admin_id`) VALUES ('".$bank_name."','sales product','".$sales_ins_id."','".$ins_id."','".$payable_amount."','".date('Y-m-d H:i:s')."','".$_SESSION['cm_id']."','".$_SESSION['admin_id']."')";
						$bank_query=mysql_query($bank);  
					}
					if($sales_ins_id >0)
					{
						?>
						<script>
						send(<?php echo $sales_ins_id; ?>);
						</script>
						<?php
					}
					//======================================Ahmedabad======================================================
					if($customer_id=="18")
					{
						if($_SESSION['branch_name']=="Pune")
						{
							$data_record_ahm['vendor_id']="53"; // ISAS Pune
							$cm_ids="60";//ahmedabad
							$data_record_ahm['cm_id']=$cm_ids;
						}
						/*elseif($_SESSION['branch_name']=="Ahmedabad")
						{
							$data_record_ahm['vendor_id']="325";
							$cm_ids="2";//pune
							$data_record_ahm['cm_id']=$cm_ids;
						}*/
						//$data_record['product_id']=$product_id;
						//$data_record['invoice_no']=$invoice_no;
						$data_record_ahm['price']=$product_price;
						$data_record_ahm['discount_type']=$discount_type;
						$data_record_ahm['discount']=$discount;
						$data_record_ahm['discount_price']=$discount_price;
						//$data_record['discount']=$discount;
						//$data_record['tax']=$tax;
						$data_record_ahm['total_cost']=$total_price;
						//$data_record['branch_id']=$branch_id;
						$data_record_ahm['payment_mode_id'] =$payment_mode_id;
						$data_record_ahm['chaque_no'] =$chaque_no;
						$data_record_ahm['chaque_date'] =$chaque_date;
						$data_record_ahm['credit_card_no'] =$credit_card_no;
						$data_record_ahm['bank_ref_no'] =$bank_ref_no;
						$data_record_ahm['bank_id'] =$bank_name;
						$data_record_ahm['amount1'] = $total_price;								
						$data_record_ahm['admin_id']=( ($_POST['stockist_id'])) ? $_POST['stockist_id'] : "0";
						$data_record_ahm['payable_amount']=$_POST['payable_amount'];
						$data_record_ahm['remaining_amount']=$_POST['remaining_amount'];
						//$data_record['ref_invoice_no']=$ref_invoice_no;
						$data_record_ahm['added_date'] =$added_date;
						//==============Update Invoice No.=====================
						$sel_inv="select ext_invoice_no from inventory where cm_id='".$data_record_ahm['cm_id']."' and ext_invoice_no IS NOT NULL order by ext_invoice_no desc limit 0,1";
						$ptr_inv=mysql_query($sel_inv);
						$data_inv=mysql_fetch_array($ptr_inv);
						
						$recp=explode("/",$data_inv['ext_invoice_no']);
						$inv_no=intval($recp[2])+1;
						$preinv=$recp[0].'/'.$recp[1].'/';
						$data_record['ext_invoice_no']=$preinv.$inv_no;
						//======================================================
						$record_id=$db->query_insert("inventory", $data_record_ahm);
						for($i=1;$i<=$total_floor;$i++)
						{
							if(trim($_POST['product_id'.$i])>0)
							{
								$data_record_service_inv['inventory_id'] =$record_id; 
								$product_id=( ($_POST['product_id'.$i])) ? $_POST['product_id'.$i] : "0";
								$data_record_service_inv['sin_product_price']=( ($_POST['prod_price'.$i])) ? $_POST['prod_price'.$i] : "0";
								$data_record_service_inv['sin_product_base_price']=( ($_POST['prod_base_price'.$i])) ? $_POST['prod_base_price'.$i] : "0";
								$data_record_service_inv['discounted_price']=( ($_POST['prod_discounted_price'.$i])) ? $_POST['prod_discounted_price'.$i] : "0";
								$data_record_service_inv['sin_product_disc']=( ($_POST['product_disc'.$i])) ? $_POST['product_disc'.$i] : "0";
								$data_record_service_inv['sin_prod_disc_price']=( ($_POST['prod_disc_price'.$i])) ? $_POST['prod_disc_price'.$i] : "0";
								$data_record_service_inv['sin_product_total']=( ($_POST['sales_product_price'.$i])) ? $_POST['sales_product_price'.$i] : "0";
								$data_record_service_inv['sin_product_qty']=( ($_POST['product_qty'.$i])) ? $_POST['product_qty'.$i] : "0";
								$data_record_service_inv['cgst_tax_in_per'] =$_POST['sin_product_cgst'.$i] ? $_POST['sin_product_cgst'.$i] : "0";
								$data_record_service_inv['cgst_tax'] =$_POST['sin_prod_cgst_price'.$i] ? $_POST['sin_prod_cgst_price'.$i] : "0";
								$data_record_service_inv['sgst_tax_in_per'] =$_POST['sin_product_sgst'.$i] ? $_POST['sin_product_sgst'.$i] : "0";
								$data_record_service_inv['sgst_tax'] =$_POST['sin_prod_sgst_price'.$i] ? $_POST['sin_prod_sgst_price'.$i] : "0";
								$data_record_service_inv['igst_tax_in_per'] =$_POST['sin_product_igst'.$i] ? $_POST['sin_product_igst'.$i] : "0";
								$data_record_service_inv['igst_tax'] =$_POST['sin_prod_igst_price'.$i] ? $_POST['sin_prod_igst_price'.$i] : "0";
								$data_record_service_inv['service_tax_in_per'] =$_POST['sin_product_vat'.$i] ? $_POST['sin_product_vat'.$i] : "0";
								
								$data_record_service_inv['service_tax'] =$_POST['sin_prod_vat_price'.$i] ? $_POST['sin_prod_vat_price'.$i] : "0";
								
								$sel_admin_id="select `admin_id` from `site_setting` where `cm_id`='".$cm_ids."' and `type`='ST' and system_status='Enabled'";
								$ptr_admin_id=mysql_query($sel_admin_id);
								$data_cm_id=mysql_fetch_array($ptr_admin_id);
								
								$sel_product_name="select product_name,product_code,pcategory_id, sub_id,size,unit, commission,price,vender,type,added_date,cm_id, quantity,admin_id from product where product_id='".$product_id."' ";
								$ptr_names=mysql_query($sel_product_name);
								if(mysql_num_rows($ptr_names))
								{
									$data_product=mysql_fetch_array($ptr_names);
																					
									$sele_cate="select product_id from product where product_name='".$data_product['product_name']."' and status='Active' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."' "; //
									$ptr_sele_catte=mysql_query($sele_cate);
									if(mysql_num_rows($ptr_sele_catte))
									{
										$data_product_id=mysql_fetch_array($ptr_sele_catte);
										$data_record_service_inv['product_id']=$data_product_id['product_id'];
										
										$update_products1="update `product` set `quantity`=(quantity+".$data_record_service_inv['sin_product_qty'].") where `product_id`='".$data_record_service_inv['product_id']."' and cm_id='".$cm_ids."' and status='Active' and admin_id='".$data_record_ahm['admin_id']."' "; //
										$query_update=mysql_query($update_products1);
									}
									else
									{
										//echo "<br/>hi..";
										$sel_category="select pcategory_name from product_category where pcategory_id='".$data_product['pcategory_id']."'";
										$ptr_category=mysql_query($sel_category);
										$data_cate=mysql_fetch_array($ptr_category);
										
										$sel_subcategory1="select sub_name from product_subcategory where sub_id='".$data_product['sub_id']."'";
										$ptr_subcategory1=mysql_query($sel_subcategory1);
										$data_subcategory=mysql_fetch_array($ptr_subcategory1);
										
										$sele_cateahm="select pcategory_id from product_category where pcategory_name='".$data_cate['pcategory_name']."' and cm_id='".$cm_ids."' order by  pcategory_id asc";
										$ptr_sele_ahmcatte=mysql_query($sele_cateahm);
										if(mysql_num_rows($ptr_sele_ahmcatte))
										{
											$data_ahm_cat=mysql_fetch_array($ptr_sele_ahmcatte);
											$cat_id=$data_ahm_cat['pcategory_id'];
										}
										else
										{
											$insert_cat="insert into product_category (`pcategory_name`,`added_date`,`cm_id`,`admin_id`) values('".$data_cate['pcategory_name']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
											$ptr_ins_cat=mysql_query($insert_cat);
											$cat_id=mysql_insert_id($ptr_ins_cat);
										}
										$sele_subcateahm="select sub_id from product_subcategory where sub_name='".$data_subcategory['sub_name']."' and cm_id='".$cm_ids."' order by  sub_id asc";
										$ptr_sele_subcatte=mysql_query($sele_subcateahm);
										if(mysql_num_rows($ptr_sele_subcatte))
										{
											$data_ahm_subcat=mysql_fetch_array($ptr_sele_subcatte);
											$sub_cat_id=$data_ahm_subcat['sub_id'];
										}
										else
										{
											"<br/>6".$insert_subcat="insert into product_subcategory (`sub_name`,`pcategory_id`,`cm_id`,`admin_id`) values('".$data_cate['pcategory_name']."','".$cat_id."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
											$ptr_ins_subcat=mysql_query($insert_subcat);
											$sub_cat_id=mysql_insert_id($ptr_ins_subcat);
										}
										$inser_prod="insert into product (`product_name`,`product_code`,`pcategory_id`,`sub_id`,`size`,`unit`,`commission`,`price`,`type`,`added_date`,`cm_id`,`quantity`,`admin_id`,`status`) values ('".$data_product['product_name']."','".$data_product['product_code']."','".$cat_id."','".$sub_cat_id."','".$data_product['size']."','".$data_product['unit']."','".$data_product['commission']."','".$data_product['price']."','".$data_product['type']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_service_inv['sin_product_qty']."','".$data_record_ahm['admin_id']."','Active')";
										$ptr_mysql_prod=mysql_query($inser_prod);
										$product_ids=mysql_insert_id();
										$data_record_service_inv['product_id']=$product_ids;
									}
								}
								$data_record_service_inv['admin_id']=$data_record_ahm['admin_id'];
								$customer_service_id=$db->query_insert("inventory_product_map", $data_record_service_inv);
								
								$sel_qty="select quantity from product where product_id='".$_POST['product_id'.$i]."' ";
								$ptr_qty=mysql_query($sel_qty);
								$data_qty=mysql_fetch_array($ptr_qty);
								//$total_quantity=intval($data_qty['quantity'])+intval($data_record_service_inv['sin_product_qty']);
								//$update_prod_qty="update product set quantity='".$total_quantity."' where product_id='".$_POST['product_id'.$i]."'";
								//$query_prod_qty=mysql_query($update_prod_qty); 
							}
						}
						
						$status='paid';
						
						if($chaque_date !='')
						{
							$chaque_date_exp=explode('/', $chaque_date);
							$sep_check_date=$chaque_date_exp[2].'-'.$chaque_date_exp[1].'-'.$chaque_date_exp[0];
						}
						else
						{
							$sep_check_date='';
						}
						//===============Update Receipt no================
						$sel_recpt="select receipt_no from inventory_invoice where cm_id='".$cm_ids."' and (receipt_no IS NOT NULL and receipt_no !='') order by receipt_no desc limit 0,1";
						$ptr_recpt=mysql_query($sel_recpt);
						$data_receipt=mysql_fetch_array($ptr_recpt);
						
						$recp=explode("-",$data_receipt['receipt_no']);
						$recpt_no=intval($recp[1])+1;
						$pre=$recp[0].'-';
						$receipt_no=$pre.$recpt_no;
						//==================================================
						"<br/>".$insert_sales_invoice = " INSERT INTO `inventory_invoice` (`inventory_id`,`receipt_no`, `price`, `total_cost`, `amount1`, `payable_amount`,`remaining_amount`, `paid_type`, `bank_id`, `cheque_detail`, `chaque_date`, `credit_card_no`,`bank_ref_no`, `admin_id`, `added_date`,`status`,`cm_id`,`total_paid`) VALUES ('".$record_id."','".$receipt_no."', '".$product_price."', '".$total_price."', '".$amount1."', '".$_POST['payable_amount']."','".$_POST['remaining_amount']."', '".$payment_mode_id."','".$bank_name."', '".$chaque_no."', '".$sep_check_date."','".$credit_card_no."','".$bank_ref_no."', '".$data_record_ahm['admin_id']."','".$added_date."','".$status."','".$cm_ids."','".$_POST['payable_amount']."'); ";
						$ptr_sales_invoice = mysql_query($insert_sales_invoice);
						//============================================================================
						$sel_cust="select name,contact from vendor where vendor_id ='".$data_record_ahm['vendor_id']."'";
						$ptr_cus_name=mysql_query($sel_cust);
						$data_cust_name=mysql_fetch_array($ptr_cus_name);
						$name=$data_cust_name['name'];
						$contact=$data_cust_name['contact'];
						$mesg ="Hi ".$name." Thanks for purchasing our service";
						$sel_inq="select sms_text from previleges where privilege_id='136'";
						$ptr_inq=mysql_query($sel_inq);
						$txt_msg='';
						if(mysql_num_rows($ptr_query))
						{
							$dta_msg=mysql_fetch_array($ptr_inq);
							$txt_msg=$dta_msg['sms_text'];
						}
					}
					//===========================================ISAS PCMC============================================
						if($customer_id=="1113")
						{
							if($_SESSION['branch_name']=="Pune")
							{
								$data_record_ahm['vendor_id']="53"; // ISAS Pune
								$cm_ids="115";//PCMC
								$data_record_ahm['cm_id']=$cm_ids;
								
							}
							/*elseif($_SESSION['branch_name']=="ISAS PCMC")
							{
								$data_record_ahm['vendor_id']="508";
								$cm_ids="115";//pune
								$data_record_ahm['cm_id']=$cm_ids;
							}*/
							elseif($_SESSION['branch_name']=="Ahmedabad")
							{
								$data_record_ahm['vendor_id']="325";
								$cm_ids="115";//Ahm
								$data_record_ahm['cm_id']=$cm_ids;
							}
							
							$data_record_ahm['price']=$product_price;
							$data_record_ahm['discount_type']=$discount_type;
							$data_record_ahm['discount']=$discount;
							$data_record_ahm['discount_price']=$discount_price;
							$data_record_ahm['total_cost']=$total_price;
							//$data_record['branch_id']=$branch_id;
							$data_record_ahm['payment_mode_id'] =$payment_mode_id;
							$data_record_ahm['chaque_no'] =$chaque_no;
							$data_record_ahm['chaque_date'] =$chaque_date;
							$data_record_ahm['credit_card_no'] =$credit_card_no;
							$data_record_ahm['bank_ref_no'] =$bank_ref_no;
							$data_record_ahm['bank_id'] =$bank_name;
							$data_record_ahm['amount1'] = $total_price;
							$data_record_ahm['admin_id']=( ($_POST['stockist_id'])) ? $_POST['stockist_id'] : "0";										
							$data_record_ahm['payable_amount']=$_POST['payable_amount'];
							$data_record_ahm['remaining_amount']=$_POST['remaining_amount'];
							//$data_record['ref_invoice_no']=$ref_invoice_no;
							$data_record_ahm['added_date'] =$added_date;
							//==============Update Invoice No.=====================
							$sel_inv="select ext_invoice_no from inventory where cm_id='".$data_record_ahm['cm_id']."' and ext_invoice_no IS NOT NULL order by ext_invoice_no desc limit 0,1";
							$ptr_inv=mysql_query($sel_inv);
							$data_inv=mysql_fetch_array($ptr_inv);
							
							$recp=explode("/",$data_inv['ext_invoice_no']);
							$inv_no=intval($recp[2])+1;
							$preinv=$recp[0].'/'.$recp[1].'/';
							$data_record['ext_invoice_no']=$preinv.$inv_no;
							//======================================================
							$record_id=$db->query_insert("inventory", $data_record_ahm);
							for($i=1;$i<=$total_floor;$i++)
							{
								if(trim($_POST['product_id'.$i]) !='')
								{
									$data_record_service_inv['inventory_id'] =$record_id; 
									$product_id=(($_POST['product_id'.$i])) ? $_POST['product_id'.$i] : "0";
									$data_record_service_inv['sin_product_price']=( ($_POST['prod_price'.$i])) ? $_POST['prod_price'.$i] : "0";
									$data_record_service_inv['sin_product_base_price']=( ($_POST['prod_base_price'.$i])) ? $_POST['prod_base_price'.$i] : "0";
									$data_record_service_inv['discounted_price']=( ($_POST['prod_discounted_price'.$i])) ? $_POST['prod_discounted_price'.$i] : "0";
									$data_record_service_inv['sin_product_disc']=( ($_POST['product_disc'.$i])) ? $_POST['product_disc'.$i] : "0";
									$data_record_service_inv['sin_prod_disc_price']=( ($_POST['prod_disc_price'.$i])) ? $_POST['prod_disc_price'.$i] : "0";
									$data_record_service_inv['sin_product_total']=( ($_POST['sales_product_price'.$i])) ? $_POST['sales_product_price'.$i] : "0";
									$data_record_service_inv['sin_product_qty']=( ($_POST['product_qty'.$i])) ? $_POST['product_qty'.$i] : "0";
									$data_record_service_inv['cgst_tax_in_per'] =$_POST['sin_product_cgst'.$i] ? $_POST['sin_product_cgst'.$i] : "0";
									$data_record_service_inv['cgst_tax'] =$_POST['sin_prod_cgst_price'.$i] ? $_POST['sin_prod_cgst_price'.$i] : "0";
									$data_record_service_inv['sgst_tax_in_per'] =$_POST['sin_product_sgst'.$i] ? $_POST['sin_product_sgst'.$i] : "0";
									$data_record_service_inv['sgst_tax'] =$_POST['sin_prod_sgst_price'.$i] ? $_POST['sin_prod_sgst_price'.$i] : "0";
									$data_record_service_inv['igst_tax_in_per'] =$_POST['sin_product_igst'.$i] ? $_POST['sin_product_igst'.$i] : "0";
									$data_record_service_inv['igst_tax'] =$_POST['sin_prod_igst_price'.$i] ? $_POST['sin_prod_igst_price'.$i] : "0";
									$data_record_service_inv['service_tax_in_per'] =$_POST['sin_product_vat'.$i] ? $_POST['sin_product_vat'.$i] : "0";
									$data_record_service_inv['service_tax'] =$_POST['sin_prod_vat_price'.$i] ? $_POST['sin_prod_vat_price'.$i] : "0";
									$sel_admin_id="select `admin_id` from `site_setting` where `cm_id`='".$cm_id1."' and `type`='ST' and system_status='Enabled'";
									$ptr_admin_id=mysql_query($sel_admin_id);
									$data_cm_id=mysql_fetch_array($ptr_admin_id);
									
									$sel_product_name="select product_name,product_code,pcategory_id,sub_id,size,unit,commission,price,vender,type,added_date,cm_id,quantity,admin_id from product where product_id='".$product_id."'";
									$ptr_names=mysql_query($sel_product_name);
									if(mysql_num_rows($ptr_names))
									{
										$data_product=mysql_fetch_array($ptr_names);
										$sele_cate="select product_id from product where product_name='".$data_product['product_name']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."' ";
										$ptr_sele_catte=mysql_query($sele_cate);
										if(mysql_num_rows($ptr_sele_catte))
										{
											$data_product_id=mysql_fetch_array($ptr_sele_catte);
											$data_record_service_inv['product_id']=$data_product_id['product_id'];
											$update_products1="update `product` set `quantity`=(quantity+".$data_record_service_inv['sin_product_qty'].") where `product_id`='".$data_record_service_inv['product_id']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."'  ";
											$query_update=mysql_query($update_products1);
										}
										else
										{
											$sel_category="select pcategory_name from product_category where pcategory_id='".$data_product['pcategory_id']."'";
											$ptr_category=mysql_query($sel_category);
											$data_cate=mysql_fetch_array($ptr_category);
											
											$sel_subcategory1="select sub_name from product_subcategory where sub_id='".$data_product['sub_id']."'";
											$ptr_subcategory1=mysql_query($sel_subcategory1);
											$data_subcategory=mysql_fetch_array($ptr_subcategory1);
											
											$sele_cateahm="select pcategory_id from product_category where pcategory_name='".$data_cate['pcategory_name']."' and cm_id='".$cm_ids."' order by pcategory_id asc";
											$ptr_sele_ahmcatte=mysql_query($sele_cateahm);
											if(mysql_num_rows($ptr_sele_ahmcatte))
											{
												$data_ahm_cat=mysql_fetch_array($ptr_sele_ahmcatte);
												$cat_id=$data_ahm_cat['pcategory_id'];
											}
											else
											{
												$insert_cat="insert into product_category (`pcategory_name`,`added_date`,`cm_id`,`admin_id`) values('".$data_cate['pcategory_name']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_cat=mysql_query($insert_cat);
												$cat_id=mysql_insert_id();
											}
											
											$sele_subcateahm="select sub_id from product_subcategory where sub_name='".$data_subcategory['sub_name']."' and cm_id='".$cm_ids."' order by  sub_id asc";
											$ptr_sele_subcatte=mysql_query($sele_subcateahm);
											if(mysql_num_rows($ptr_sele_subcatte))
											{
												$data_ahm_subcat=mysql_fetch_array($ptr_sele_subcatte);
												$sub_cat_id=$data_ahm_subcat['sub_id'];
											}
											else
											{
												$insert_subcat="insert into product_subcategory (`sub_name`,`pcategory_id`,`cm_id`,`admin_id`) values('".$data_subcategory['sub_name']."','".$cat_id."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_subcat=mysql_query($insert_subcat);
												$sub_cat_id=mysql_insert_id();
											}											
											$inser_prod="insert into product (`product_name`,`product_code`,`pcategory_id`,`sub_id`,`size`,`unit`,`commission`,`price`,`type`,`added_date`,`cm_id`,`quantity`,`admin_id`,`status`) values ('".$data_product['product_name']."','".$data_product['product_code']."','".$cat_id."','".$sub_cat_id."','".$data_product['size']."','".$data_product['unit']."','".$data_product['commission']."','".$data_product['price']."','".$data_product['type']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_service_inv['sin_product_qty']."','".$data_record_ahm['admin_id']."','Active')";
											$ptr_mysql_prod=mysql_query($inser_prod);
											$product_ids=mysql_insert_id();
											$data_record_service_inv['product_id']=$product_ids;
										}
									}
									$data_record_service_inv['admin_id']=$data_record_ahm['admin_id'];
									$customer_service_id=$db->query_insert("inventory_product_map", $data_record_service_inv);
									
									$sel_qty="select quantity from product where product_id='".$_POST['product_id'.$i]."' ";
									$ptr_qty=mysql_query($sel_qty);
									$data_qty=mysql_fetch_array($ptr_qty);
									
								}
							}
							/*for($j=1;$j<=$total_type1;$j++) 25/6/18
							{
								$data_record_tax_inv['inventory_id'] =$record_id; 
								$data_record_tax_inv['tax_type'] =$_POST['tax_type'.$j];
								$data_record_tax_inv['tax_value'] =$_POST['tax_value'.$j];
								$data_record_tax_inv['tax_amount']=$_POST['tax_amount'.$j];
								$customer_tax_id=$db->query_insert("inventory_tax_map", $data_record_tax_inv);
							}*/
							
							//"<br>".$insert="INSERT INTO `log_history`(`category`, `action`, `name`, `id`, `date`, `cm_id`, `admin_id`) VALUES ('add_inventory','Add','add inventory','".$record_id."','".date('Y-m-d H:i:s')."','".$_SESSION['cm_id']."','".$_SESSION['admin_id']."')";
							//$query=mysql_query($insert);
								
							/*if($payment_type_val=="online")
							$status='pending';
							else*/
							$status='paid';
							
							if($chaque_date !='')
							{
								$chaque_date_exp=explode('/', $chaque_date);
								$sep_check_date=$chaque_date_exp[2].'-'.$chaque_date_exp[1].'-'.$chaque_date_exp[0];
							}
							else
							{
								$sep_check_date='';
							}
							//===============Update Receipt no================
							$sel_recpt="select receipt_no from inventory_invoice where cm_id='".$cm_ids."' and (receipt_no IS NOT NULL and receipt_no !='') order by receipt_no desc limit 0,1";
							$ptr_recpt=mysql_query($sel_recpt);
							$data_receipt=mysql_fetch_array($ptr_recpt);
							
							$recp=explode("-",$data_receipt['receipt_no']);
							$recpt_no=intval($recp[1])+1;
							$pre=$recp[0].'-';
							$receipt_no=$pre.$recpt_no;
							//==================================================
							//$data_record_ahm['bank_ref_no'] =$bank_ref_no;
							$insert_sales_invoice ="INSERT INTO `inventory_invoice` (`inventory_id`,`receipt_no`, `price`, `total_cost`, `amount1`, `payable_amount`,`remaining_amount`, `paid_type`, `bank_id`, `cheque_detail`, `chaque_date`, `credit_card_no`,`bank_ref_no`, `admin_id`, `added_date`,`status`,`cm_id`,`total_paid`) VALUES ('".$record_id."','".$receipt_no."', '".$product_price."', '".$total_price."', '".$amount1."', '".$_POST['payable_amount']."','".$_POST['remaining_amount']."', '".$payment_mode_id."','".$bank_name."', '".$chaque_no."', '".$sep_check_date."','".$credit_card_no."','".$bank_ref_no."', '".$data_record_ahm['admin_id']."','".$added_date."','".$status."','".$cm_ids."','".$_POST['payable_amount']."'); ";
							$ptr_sales_invoice = mysql_query($insert_sales_invoice);
							//============================================================================
							$sel_cust="select name,contact from vendor where vendor_id ='".$data_record_ahm['vendor_id']."'";
							$ptr_cus_name=mysql_query($sel_cust);
							$data_cust_name=mysql_fetch_array($ptr_cus_name);
							$name=$data_cust_name['name'];
							$contact=$data_cust_name['contact'];
							$mesg ="Hi ".$name." Thanks for purchasing our service";
							$sel_inq="select sms_text from previleges where privilege_id='136'";
							$ptr_inq=mysql_query($sel_inq);
							$txt_msg='';
							if(mysql_num_rows($ptr_query))
							{
								$dta_msg=mysql_fetch_array($ptr_inq);
								$txt_msg=$dta_msg['sms_text'];
							}
						}
					//=================================================================================================
					//===========================================ISAS Ahmednagar============================================
						if($customer_id=="1212")
						{
							if($_SESSION['branch_name']=="Pune")
							{
								$data_record_ahm['vendor_id']="53";
								$cm_ids="119";//Ahmednagar
								$data_record_ahm['cm_id']=$cm_ids;
								
							}
							else if($_SESSION['branch_name']=="Ahmednagar")
							{
								$data_record_ahm['vendor_id']="517";
								$cm_ids="2";//pune
								$data_record_ahm['cm_id']=$cm_ids;
							}
							//$data_record['product_id']=$product_id;
							//$data_record['invoice_no']=$invoice_no;
							$data_record_ahm['price']=$product_price;
							$data_record_ahm['discount_type']=$discount_type;
							$data_record_ahm['discount']=$discount;
							$data_record_ahm['discount_price']=$discount_price;
							//$data_record['discount']=$discount;
							//$data_record['tax']=$tax;
							$data_record_ahm['total_cost']=$total_price;
							//$data_record['branch_id']=$branch_id;
							$data_record_ahm['payment_mode_id'] =$payment_mode_id;
							$data_record_ahm['chaque_no'] =$chaque_no;
							$data_record_ahm['chaque_date'] =$chaque_date;
							$data_record_ahm['credit_card_no'] =$credit_card_no;
							$data_record_ahm['bank_ref_no'] =$bank_ref_no;
							$data_record_ahm['bank_id'] =$bank_name;
							$data_record_ahm['amount1'] = $total_price;
							
							$data_record_ahm['admin_id']=( ($_POST['stockist_id'])) ? $_POST['stockist_id'] : "0";
							
							$data_record_ahm['payable_amount']=$_POST['payable_amount'];
							$data_record_ahm['remaining_amount']=$_POST['remaining_amount'];
							//$data_record['ref_invoice_no']=$ref_invoice_no;
							
							$data_record_ahm['added_date'] =$added_date;
							//==============Update Invoice No.=====================
							$sel_inv="select ext_invoice_no from inventory where cm_id='".$data_record_ahm['cm_id']."' and ext_invoice_no IS NOT NULL order by ext_invoice_no desc limit 0,1";
							$ptr_inv=mysql_query($sel_inv);
							$data_inv=mysql_fetch_array($ptr_inv);
							
							$recp=explode("/",$data_inv['ext_invoice_no']);
							$inv_no=intval($recp[2])+1;
							$preinv=$recp[0].'/'.$recp[1].'/';
							$data_record['ext_invoice_no']=$preinv.$inv_no;
							//======================================================
							$record_id=$db->query_insert("inventory", $data_record_ahm);
							for($i=1;$i<=$total_floor;$i++)
							{
								if(trim($_POST['product_id'.$i]) !='')
								{
									$data_record_service_inv['inventory_id'] =$record_id; 
									$product_id=( ($_POST['product_id'.$i])) ? $_POST['product_id'.$i] : "0";
									$data_record_service_inv['sin_product_price']=( ($_POST['prod_price'.$i])) ? $_POST['prod_price'.$i] : "0";
									$data_record_service_inv['sin_product_base_price']=( ($_POST['prod_base_price'.$i])) ? $_POST['prod_base_price'.$i] : "0";
									$data_record_service_inv['discounted_price']=( ($_POST['prod_discounted_price'.$i])) ? $_POST['prod_discounted_price'.$i] : "0";
									$data_record_service_inv['sin_product_disc']=( ($_POST['product_disc'.$i])) ? $_POST['product_disc'.$i] : "0";
									$data_record_service_inv['sin_prod_disc_price']=( ($_POST['prod_disc_price'.$i])) ? $_POST['prod_disc_price'.$i] : "0";
									$data_record_service_inv['sin_product_total']=( ($_POST['sales_product_price'.$i])) ? $_POST['sales_product_price'.$i] : "0";
									$data_record_service_inv['sin_product_qty']=( ($_POST['product_qty'.$i])) ? $_POST['product_qty'.$i] : "0";
									$data_record_service_inv['cgst_tax_in_per'] =$_POST['sin_product_cgst'.$i] ? $_POST['sin_product_cgst'.$i] : "0";
									$data_record_service_inv['cgst_tax'] =$_POST['sin_prod_cgst_price'.$i] ? $_POST['sin_prod_cgst_price'.$i] : "0";
									$data_record_service_inv['sgst_tax_in_per'] =$_POST['sin_product_sgst'.$i] ? $_POST['sin_product_sgst'.$i] : "0";
									$data_record_service_inv['sgst_tax'] =$_POST['sin_prod_sgst_price'.$i] ? $_POST['sin_prod_sgst_price'.$i] : "0";
									$data_record_service_inv['igst_tax_in_per'] =$_POST['sin_product_igst'.$i] ? $_POST['sin_product_igst'.$i] : "0";
									$data_record_service_inv['igst_tax'] =$_POST['sin_prod_igst_price'.$i] ? $_POST['sin_prod_igst_price'.$i] : "0";
									$data_record_service_inv['service_tax_in_per'] =$_POST['sin_product_vat'.$i] ? $_POST['sin_product_vat'.$i] : "0";
									$data_record_service_inv['service_tax'] =$_POST['sin_prod_vat_price'.$i] ? $_POST['sin_prod_vat_price'.$i] : "0";
									"<br/>".$sel_admin_id="select `admin_id` from `site_setting` where `cm_id`='".$cm_id1."' and `type`='ST'";
									$ptr_admin_id=mysql_query($sel_admin_id);
									$data_cm_id=mysql_fetch_array($ptr_admin_id);
									
									"<br/>".$sel_product_name=" select product_name,product_code,pcategory_id,sub_id,size,unit,commission,price,vender,type,added_date,cm_id,quantity,admin_id from product where product_id='".$product_id."' ";
									$ptr_names=mysql_query($sel_product_name);
									if(mysql_num_rows($ptr_names))
									{
										$data_product=mysql_fetch_array($ptr_names);
																						
										"<br/>".$sele_cate="select product_id from product where product_name='".$data_product['product_name']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."' ";
										$ptr_sele_catte=mysql_query($sele_cate);
										if(mysql_num_rows($ptr_sele_catte))
										{
											$data_product_id=mysql_fetch_array($ptr_sele_catte);
											$data_record_service_inv['product_id']=$data_product_id['product_id'];
											
											$update_products1="update `product` set `quantity`=(quantity+".$data_record_service_inv['sin_product_qty'].") where `product_id`='".$data_record_service_inv['product_id']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."'  ";
											$query_update=mysql_query($update_products1);
										}
										else
										{
											//echo "<br/>hi..";
											"<br/>1".$sel_category="select pcategory_name from product_category where pcategory_id='".$data_product['pcategory_id']."'";
											$ptr_category=mysql_query($sel_category);
											$data_cate=mysql_fetch_array($ptr_category);
											
											"<br/>2".$sel_subcategory1="select sub_name from product_subcategory where sub_id='".$data_product['sub_id']."'";
											$ptr_subcategory1=mysql_query($sel_subcategory1);
											$data_subcategory=mysql_fetch_array($ptr_subcategory1);
											
											"<br/>3".$sele_cateahm="select pcategory_id from product_category where pcategory_name='".$data_cate['pcategory_name']."' and cm_id='".$cm_ids."' order by  pcategory_id asc";
											$ptr_sele_ahmcatte=mysql_query($sele_cateahm);
											if(mysql_num_rows($ptr_sele_ahmcatte))
											{
												$data_ahm_cat=mysql_fetch_array($ptr_sele_ahmcatte);
												$cat_id=$data_ahm_cat['pcategory_id'];
											}
											else
											{
												"<br/>4".$insert_cat="insert into product_category (`pcategory_name`,`added_date`,`cm_id`,`admin_id`) values('".$data_cate['pcategory_name']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_cat=mysql_query($insert_cat);
												$cat_id=mysql_insert_id();
											}
											
											"<br/>5".$sele_subcateahm="select sub_id from product_subcategory where sub_name='".$data_subcategory['sub_name']."' and cm_id='".$cm_ids."' order by  sub_id asc";
											$ptr_sele_subcatte=mysql_query($sele_subcateahm);
											if(mysql_num_rows($ptr_sele_subcatte))
											{
												$data_ahm_subcat=mysql_fetch_array($ptr_sele_subcatte);
												$sub_cat_id=$data_ahm_subcat['sub_id'];
											}
											else
											{
												"<br/>6".$insert_subcat="insert into product_subcategory (`sub_name`,`pcategory_id`,`cm_id`,`admin_id`) values('".$data_subcategory['sub_name']."','".$cat_id."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_subcat=mysql_query($insert_subcat);
												$sub_cat_id=mysql_insert_id();
											}
																						
											"<br/>7".$inser_prod="insert into product (`product_name`,`product_code`,`pcategory_id`,`sub_id`,`size`,`unit`,`commission`,`price`,`type`,`added_date`,`cm_id`,`quantity`,`admin_id`,`status`) values ('".$data_product['product_name']."','".$data_product['product_code']."','".$cat_id."','".$sub_cat_id."','".$data_product['size']."','".$data_product['unit']."','".$data_product['commission']."','".$data_product['price']."','".$data_product['type']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_service_inv['sin_product_qty']."','".$data_record_ahm['admin_id']."','Active')";
											$ptr_mysql_prod=mysql_query($inser_prod);
											$product_ids=mysql_insert_id();
											$data_record_service_inv['product_id']=$product_ids;
										}
									}
									$data_record_service_inv['admin_id']=$data_record_ahm['admin_id'];
									$customer_service_id=$db->query_insert("inventory_product_map", $data_record_service_inv);
									
									$sel_qty="select quantity from product where product_id='".$_POST['product_id'.$i]."' ";
									$ptr_qty=mysql_query($sel_qty);
									$data_qty=mysql_fetch_array($ptr_qty);
									//$total_quantity=intval($data_qty['quantity'])+intval($data_record_service_inv['sin_product_qty']);
									//$update_prod_qty="update product set quantity='".$total_quantity."' where product_id='".$_POST['product_id'.$i]."'";
									//$query_prod_qty=mysql_query($update_prod_qty); 
								}
							}
							/*for($j=1;$j<=$total_type1;$j++) 25/6/18
							{
								$data_record_tax_inv['inventory_id'] =$record_id; 
								$data_record_tax_inv['tax_type'] =$_POST['tax_type'.$j];
								$data_record_tax_inv['tax_value'] =$_POST['tax_value'.$j];
								$data_record_tax_inv['tax_amount']=$_POST['tax_amount'.$j];
								$customer_tax_id=$db->query_insert("inventory_tax_map", $data_record_tax_inv);
							}*/
							
							//"<br>".$insert="INSERT INTO `log_history`(`category`, `action`, `name`, `id`, `date`, `cm_id`, `admin_id`) VALUES ('add_inventory','Add','add inventory','".$record_id."','".date('Y-m-d H:i:s')."','".$_SESSION['cm_id']."','".$_SESSION['admin_id']."')";
							//$query=mysql_query($insert);
								
							/*if($payment_type_val=="online")
							$status='pending';
							else*/
							$status='paid';
							
							if($chaque_date !='')
							{
								$chaque_date_exp=explode('/', $chaque_date);
								$sep_check_date=$chaque_date_exp[2].'-'.$chaque_date_exp[1].'-'.$chaque_date_exp[0];
							}
							else
							{
								$sep_check_date='';
							}
							//===============Update Receipt no================
							$sel_recpt="select receipt_no from inventory_invoice where cm_id='".$cm_ids."' and (receipt_no IS NOT NULL and receipt_no !='') order by receipt_no desc limit 0,1";
							$ptr_recpt=mysql_query($sel_recpt);
							$data_receipt=mysql_fetch_array($ptr_recpt);
							
							$recp=explode("-",$data_receipt['receipt_no']);
							$recpt_no=intval($recp[1])+1;
							$pre=$recp[0].'-';
							$receipt_no=$pre.$recpt_no;
							//==================================================
							"<br/>".$insert_sales_invoice = " INSERT INTO `inventory_invoice` (`inventory_id`,`receipt_no`, `price`, `total_cost`, `amount1`, `payable_amount`,`remaining_amount`, `paid_type`, `bank_id`, `cheque_detail`, `chaque_date`, `credit_card_no`, `bank_ref_no`,`admin_id`, `added_date`,`status`,`cm_id`,`total_paid`) VALUES ('".$record_id."','".$receipt_no."', '".$product_price."', '".$total_price."', '".$amount1."', '".$_POST['payable_amount']."','".$_POST['remaining_amount']."', '".$payment_mode_id."','".$bank_name."', '".$chaque_no."', '".$sep_check_date."','".$credit_card_no."','".$bank_ref_no."', '".$data_record_ahm['admin_id']."','".$added_date."','".$status."','".$cm_ids."','".$_POST['payable_amount']."'); ";
							$ptr_sales_invoice = mysql_query($insert_sales_invoice);
							//============================================================================
							$sel_cust="select name,contact from vendor where vendor_id ='".$data_record_ahm['vendor_id']."'";
							$ptr_cus_name=mysql_query($sel_cust);
							$data_cust_name=mysql_fetch_array($ptr_cus_name);
							$name=$data_cust_name['name'];
							$contact=$data_cust_name['contact'];
							$mesg ="Hi ".$name." Thanks for purchasing our service";
							$sel_inq="select sms_text from previleges where privilege_id='136'";
							$ptr_inq=mysql_query($sel_inq);
							$txt_msg='';
							if(mysql_num_rows($ptr_query))
							{
								$dta_msg=mysql_fetch_array($ptr_inq);
								$txt_msg=$dta_msg['sms_text'];
							}
						}
					//=================================================================================================
					//===========================================ISAS Pune============================================
						if($customer_id=="1805")
						{
							if($_SESSION['branch_name']=="Ahmedabad")
							{
								$data_record_ahm['vendor_id']="325";
								$cm_ids="2";//ahmedabad
								$data_record_ahm['cm_id']=$cm_ids;
							}
							elseif($_SESSION['branch_name']=="Pune")
							{
								$data_record_ahm['vendor_id']="53";
								$cm_ids="60";//pune
								$data_record_ahm['cm_id']=$cm_ids;
							}
							//$data_record['product_id']=$product_id;
							//$data_record['invoice_no']=$invoice_no;
							$data_record_ahm['price']=$product_price;
							$data_record_ahm['discount_type']=$discount_type;
							$data_record_ahm['discount']=$discount;
							$data_record_ahm['discount_price']=$discount_price;
							//$data_record['discount']=$discount;
							//$data_record['tax']=$tax;
							$data_record_ahm['total_cost']=$total_price;
							//$data_record['branch_id']=$branch_id;
							$data_record_ahm['payment_mode_id'] =$payment_mode_id;
							$data_record_ahm['chaque_no'] =$chaque_no;
							$data_record_ahm['chaque_date'] =$chaque_date;
							$data_record_ahm['credit_card_no'] =$credit_card_no;
							$data_record_ahm['bank_ref_no'] =$bank_ref_no;
							$data_record_ahm['bank_id'] =$bank_name;
							$data_record_ahm['amount1'] = $total_price;
							
							$data_record_ahm['admin_id']=( ($_POST['stockist_id'])) ? $_POST['stockist_id'] : "0";
							
							$data_record_ahm['payable_amount']=$_POST['payable_amount'];
							$data_record_ahm['remaining_amount']=$_POST['remaining_amount'];
							//$data_record['ref_invoice_no']=$ref_invoice_no;
							
							$data_record_ahm['added_date'] =$added_date;
							//==============Update Invoice No.=====================
							$sel_inv="select ext_invoice_no from inventory where cm_id='".$data_record_ahm['cm_id']."' and ext_invoice_no IS NOT NULL order by ext_invoice_no desc limit 0,1";
							$ptr_inv=mysql_query($sel_inv);
							$data_inv=mysql_fetch_array($ptr_inv);
							
							$recp=explode("/",$data_inv['ext_invoice_no']);
							$inv_no=intval($recp[2])+1;
							$preinv=$recp[0].'/'.$recp[1].'/';
							$data_record['ext_invoice_no']=$preinv.$inv_no;
							//======================================================
							$record_id=$db->query_insert("inventory", $data_record_ahm);
							for($i=1;$i<=$total_floor;$i++)
							{
								if(trim($_POST['product_id'.$i]) !='')
								{
									$data_record_service_inv['inventory_id'] =$record_id; 
									$product_id=( ($_POST['product_id'.$i])) ? $_POST['product_id'.$i] : "0";
									$data_record_service_inv['sin_product_price']=( ($_POST['prod_price'.$i])) ? $_POST['prod_price'.$i] : "0";
									$data_record_service_inv['sin_product_base_price']=( ($_POST['prod_base_price'.$i])) ? $_POST['prod_base_price'.$i] : "0";
									$data_record_service_inv['discounted_price']=( ($_POST['prod_discounted_price'.$i])) ? $_POST['prod_discounted_price'.$i] : "0";
									$data_record_service_inv['sin_product_disc']=( ($_POST['product_disc'.$i])) ? $_POST['product_disc'.$i] : "0";
									$data_record_service_inv['sin_prod_disc_price']=( ($_POST['prod_disc_price'.$i])) ? $_POST['prod_disc_price'.$i] : "0";
									$data_record_service_inv['sin_product_total']=( ($_POST['sales_product_price'.$i])) ? $_POST['sales_product_price'.$i] : "0";
									$data_record_service_inv['sin_product_qty']=( ($_POST['product_qty'.$i])) ? $_POST['product_qty'.$i] : "0";
									$data_record_service_inv['cgst_tax_in_per'] =$_POST['sin_product_cgst'.$i] ? $_POST['sin_product_cgst'.$i] : "0";
									$data_record_service_inv['cgst_tax'] =$_POST['sin_prod_cgst_price'.$i] ? $_POST['sin_prod_cgst_price'.$i] : "0";
									$data_record_service_inv['sgst_tax_in_per'] =$_POST['sin_product_sgst'.$i] ? $_POST['sin_product_sgst'.$i] : "0";
									$data_record_service_inv['sgst_tax'] =$_POST['sin_prod_sgst_price'.$i] ? $_POST['sin_prod_sgst_price'.$i] : "0";
									$data_record_service_inv['igst_tax_in_per'] =$_POST['sin_product_igst'.$i] ? $_POST['sin_product_igst'.$i] : "0";
									$data_record_service_inv['igst_tax'] =$_POST['sin_prod_igst_price'.$i] ? $_POST['sin_prod_igst_price'.$i] : "0";
									$data_record_service_inv['service_tax_in_per'] =$_POST['sin_product_vat'.$i] ? $_POST['sin_product_vat'.$i] : "0";
									$data_record_service_inv['service_tax'] =$_POST['sin_prod_vat_price'.$i] ? $_POST['sin_prod_vat_price'.$i] : "0";
									$sel_admin_id="select `admin_id` from `site_setting` where `cm_id`='".$cm_id1."' and `type`='ST'";
									$ptr_admin_id=mysql_query($sel_admin_id);
									$data_cm_id=mysql_fetch_array($ptr_admin_id);
									
									$sel_product_name=" select product_name,product_code,pcategory_id,sub_id,size,unit,commission,price,vender,type,added_date,cm_id,quantity,admin_id from product where product_id='".$product_id."' ";
									$ptr_names=mysql_query($sel_product_name);
									if(mysql_num_rows($ptr_names))
									{
										$data_product=mysql_fetch_array($ptr_names);
										$sele_cate="select product_id from product where product_name='".$data_product['product_name']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."' ";
										$ptr_sele_catte=mysql_query($sele_cate);
										if(mysql_num_rows($ptr_sele_catte))
										{
											$data_product_id=mysql_fetch_array($ptr_sele_catte);
											$data_record_service_inv['product_id']=$data_product_id['product_id'];
											
											$update_products1="update `product` set `quantity`=(quantity+".$data_record_service_inv['sin_product_qty'].") where `product_id`='".$data_record_service_inv['product_id']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."'  ";
											$query_update=mysql_query($update_products1);
										}
										else
										{
											$sel_category="select pcategory_name from product_category where pcategory_id='".$data_product['pcategory_id']."'";
											$ptr_category=mysql_query($sel_category);
											$data_cate=mysql_fetch_array($ptr_category);
											
											$sel_subcategory1="select sub_name from product_subcategory where sub_id='".$data_product['sub_id']."'";
											$ptr_subcategory1=mysql_query($sel_subcategory1);
											$data_subcategory=mysql_fetch_array($ptr_subcategory1);
											
											$sele_cateahm="select pcategory_id from product_category where pcategory_name='".$data_cate['pcategory_name']."' and cm_id='".$cm_ids."' order by  pcategory_id asc";
											$ptr_sele_ahmcatte=mysql_query($sele_cateahm);
											if(mysql_num_rows($ptr_sele_ahmcatte))
											{
												$data_ahm_cat=mysql_fetch_array($ptr_sele_ahmcatte);
												$cat_id=$data_ahm_cat['pcategory_id'];
											}
											else
											{
												$insert_cat="insert into product_category (`pcategory_name`,`added_date`,`cm_id`,`admin_id`) values('".$data_cate['pcategory_name']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_cat=mysql_query($insert_cat);
												$cat_id=mysql_insert_id();
											}
											$sele_subcateahm="select sub_id from product_subcategory where sub_name='".$data_subcategory['sub_name']."' and cm_id='".$cm_ids."' order by  sub_id asc";
											$ptr_sele_subcatte=mysql_query($sele_subcateahm);
											if(mysql_num_rows($ptr_sele_subcatte))
											{
												$data_ahm_subcat=mysql_fetch_array($ptr_sele_subcatte);
												$sub_cat_id=$data_ahm_subcat['sub_id'];
											}
											else
											{
												$insert_subcat="insert into product_subcategory (`sub_name`,`pcategory_id`,`cm_id`,`admin_id`) values('".$data_subcategory['sub_name']."','".$cat_id."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_subcat=mysql_query($insert_subcat);
												$sub_cat_id=mysql_insert_id();
											}
																						
											"<br/>7".$inser_prod="insert into product (`product_name`,`product_code`,`pcategory_id`,`sub_id`,`size`,`unit`,`commission`,`price`,`type`,`added_date`,`cm_id`,`quantity`,`admin_id`,`status`) values ('".$data_product['product_name']."','".$data_product['product_code']."','".$cat_id."','".$sub_cat_id."','".$data_product['size']."','".$data_product['unit']."','".$data_product['commission']."','".$data_product['price']."','".$data_product['type']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_service_inv['sin_product_qty']."','".$data_record_ahm['admin_id']."','Active')";
											$ptr_mysql_prod=mysql_query($inser_prod);
											$product_ids=mysql_insert_id();
											$data_record_service_inv['product_id']=$product_ids;
										}
									}
									$data_record_service_inv['admin_id']=$data_record_ahm['admin_id'];
									$customer_service_id=$db->query_insert("inventory_product_map", $data_record_service_inv);
									
									$sel_qty="select quantity from product where product_id='".$_POST['product_id'.$i]."' ";
									$ptr_qty=mysql_query($sel_qty);
									$data_qty=mysql_fetch_array($ptr_qty);
									//$total_quantity=intval($data_qty['quantity'])+intval($data_record_service_inv['sin_product_qty']);
									//$update_prod_qty="update product set quantity='".$total_quantity."' where product_id='".$_POST['product_id'.$i]."'";
									//$query_prod_qty=mysql_query($update_prod_qty); 
								}
							}
							/*for($j=1;$j<=$total_type1;$j++) 25/6/18
							{
								$data_record_tax_inv['inventory_id'] =$record_id; 
								$data_record_tax_inv['tax_type'] =$_POST['tax_type'.$j];
								$data_record_tax_inv['tax_value'] =$_POST['tax_value'.$j];
								$data_record_tax_inv['tax_amount']=$_POST['tax_amount'.$j];
								$customer_tax_id=$db->query_insert("inventory_tax_map", $data_record_tax_inv);
							}*/
							
							//"<br>".$insert="INSERT INTO `log_history`(`category`, `action`, `name`, `id`, `date`, `cm_id`, `admin_id`) VALUES ('add_inventory','Add','add inventory','".$record_id."','".date('Y-m-d H:i:s')."','".$_SESSION['cm_id']."','".$_SESSION['admin_id']."')";
							//$query=mysql_query($insert);
								
							/*if($payment_type_val=="online")
							$status='pending';
							else*/
							$status='paid';
							
							if($chaque_date !='')
							{
								$chaque_date_exp=explode('/', $chaque_date);
								$sep_check_date=$chaque_date_exp[2].'-'.$chaque_date_exp[1].'-'.$chaque_date_exp[0];
							}
							else
							{
								$sep_check_date='';
							}
							//===============Update Receipt no================
							$sel_recpt="select receipt_no from inventory_invoice where cm_id='".$cm_ids."' and (receipt_no IS NOT NULL and receipt_no !='') order by receipt_no desc limit 0,1";
							$ptr_recpt=mysql_query($sel_recpt);
							$data_receipt=mysql_fetch_array($ptr_recpt);
							
							$recp=explode("-",$data_receipt['receipt_no']);
							$recpt_no=intval($recp[1])+1;
							$pre=$recp[0].'-';
							$receipt_no=$pre.$recpt_no;
							//==================================================
							"<br/>".$insert_sales_invoice = " INSERT INTO `inventory_invoice` (`inventory_id`,`receipt_no`, `price`, `total_cost`, `amount1`, `payable_amount`,`remaining_amount`, `paid_type`, `bank_id`, `cheque_detail`, `chaque_date`, `credit_card_no`,`bank_ref_no`, `admin_id`, `added_date`,`status`,`cm_id`,`total_paid`) VALUES ('".$record_id."','".$receipt_no."', '".$product_price."', '".$total_price."', '".$amount1."', '".$_POST['payable_amount']."','".$_POST['remaining_amount']."', '".$payment_mode_id."','".$bank_name."', '".$chaque_no."', '".$sep_check_date."','".$credit_card_no."','".$bank_ref_no."', '".$data_record_ahm['admin_id']."','".$added_date."','".$status."','".$cm_ids."','".$_POST['payable_amount']."'); ";
							$ptr_sales_invoice = mysql_query($insert_sales_invoice);
							//============================================================================
							$sel_cust="select name,contact from vendor where vendor_id ='".$data_record_ahm['vendor_id']."'";
							$ptr_cus_name=mysql_query($sel_cust);
							$data_cust_name=mysql_fetch_array($ptr_cus_name);
							$name=$data_cust_name['name'];
							$contact=$data_cust_name['contact'];
							$mesg ="Hi ".$name." Thanks for purchasing our service";
							$sel_inq="select sms_text from previleges where privilege_id='136'";
							$ptr_inq=mysql_query($sel_inq);
							$txt_msg='';
							if(mysql_num_rows($ptr_query))
							{
								$dta_msg=mysql_fetch_array($ptr_inq);
								$txt_msg=$dta_msg['sms_text'];
							}
						}
					//===========================================ISAS Singhgad============================================
						if($customer_id=="2915")
						{
							if($_SESSION['branch_name']=="Pune")
							{
								$data_record_ahm['vendor_id']="53";
								$cm_ids="174";//PCMC
								$data_record_ahm['cm_id']=$cm_ids;
								
							}
							elseif($_SESSION['branch_name']=="ISAS PCMC")
							{
								$data_record_ahm['vendor_id']="508";
								$cm_ids="174";//pune
								$data_record_ahm['cm_id']=$cm_ids;
							}
							elseif($_SESSION['branch_name']=="Ahmedabad")
							{
								$data_record_ahm['vendor_id']="325";
								$cm_ids="174";//Ahm
								$data_record_ahm['cm_id']=$cm_ids;
							}
							
							$data_record_ahm['price']=$product_price;
							$data_record_ahm['discount_type']=$discount_type;
							$data_record_ahm['discount']=$discount;
							$data_record_ahm['discount_price']=$discount_price;
							$data_record_ahm['total_cost']=$total_price;
							//$data_record['branch_id']=$branch_id;
							$data_record_ahm['payment_mode_id'] =$payment_mode_id;
							$data_record_ahm['chaque_no'] =$chaque_no;
							$data_record_ahm['chaque_date'] =$chaque_date;
							$data_record_ahm['credit_card_no'] =$credit_card_no;
							$data_record_ahm['bank_ref_no'] =$bank_ref_no;
							$data_record_ahm['bank_id'] =$bank_name;
							$data_record_ahm['amount1'] = $total_price;
							$data_record_ahm['admin_id']=($_POST['stockist_id']) ? $_POST['stockist_id'] : "0";										
							$data_record_ahm['payable_amount']=$_POST['payable_amount'];
							$data_record_ahm['remaining_amount']=$_POST['remaining_amount'];
							//$data_record['ref_invoice_no']=$ref_invoice_no;
							$data_record_ahm['added_date'] =$added_date;
							//==============Update Invoice No.=====================
							$sel_inv="select ext_invoice_no from inventory where cm_id='".$data_record_ahm['cm_id']."' and ext_invoice_no IS NOT NULL order by ext_invoice_no desc limit 0,1";
							$ptr_inv=mysql_query($sel_inv);
							$data_inv=mysql_fetch_array($ptr_inv);
							
							$recp=explode("/",$data_inv['ext_invoice_no']);
							$inv_no=intval($recp[2])+1;
							$preinv=$recp[0].'/'.$recp[1].'/';
							$data_record['ext_invoice_no']=$preinv.$inv_no;
							//======================================================
							$record_id=$db->query_insert("inventory", $data_record_ahm);
							for($i=1;$i<=$total_floor;$i++)
							{
								if(trim($_POST['product_id'.$i]) !='')
								{
									$data_record_service_inv['inventory_id'] =$record_id; 
									$product_id=( ($_POST['product_id'.$i])) ? $_POST['product_id'.$i] : "0";
									$data_record_service_inv['sin_product_price']=( ($_POST['prod_price'.$i])) ? $_POST['prod_price'.$i] : "0";
									$data_record_service_inv['sin_product_base_price']=( ($_POST['prod_base_price'.$i])) ? $_POST['prod_base_price'.$i] : "0";
									$data_record_service_inv['discounted_price']=( ($_POST['prod_discounted_price'.$i])) ? $_POST['prod_discounted_price'.$i] : "0";
									$data_record_service_inv['sin_product_disc']=( ($_POST['product_disc'.$i])) ? $_POST['product_disc'.$i] : "0";
									$data_record_service_inv['sin_prod_disc_price']=( ($_POST['prod_disc_price'.$i])) ? $_POST['prod_disc_price'.$i] : "0";
									$data_record_service_inv['sin_product_total']=( ($_POST['sales_product_price'.$i])) ? $_POST['sales_product_price'.$i] : "0";
									$data_record_service_inv['sin_product_qty']=( ($_POST['product_qty'.$i])) ? $_POST['product_qty'.$i] : "0";
									$data_record_service_inv['cgst_tax_in_per'] =$_POST['sin_product_cgst'.$i] ? $_POST['sin_product_cgst'.$i] : "0";
									$data_record_service_inv['cgst_tax'] =$_POST['sin_prod_cgst_price'.$i] ? $_POST['sin_prod_cgst_price'.$i] : "0";
									$data_record_service_inv['sgst_tax_in_per'] =$_POST['sin_product_sgst'.$i] ? $_POST['sin_product_sgst'.$i] : "0";
									$data_record_service_inv['sgst_tax'] =$_POST['sin_prod_sgst_price'.$i] ? $_POST['sin_prod_sgst_price'.$i] : "0";
									$data_record_service_inv['igst_tax_in_per'] =$_POST['sin_product_igst'.$i] ? $_POST['sin_product_igst'.$i] : "0";
									$data_record_service_inv['igst_tax'] =$_POST['sin_prod_igst_price'.$i] ? $_POST['sin_prod_igst_price'.$i] : "0";
									$data_record_service_inv['service_tax_in_per'] =$_POST['sin_product_vat'.$i] ? $_POST['sin_product_vat'.$i] : "0";
									$data_record_service_inv['service_tax'] =$_POST['sin_prod_vat_price'.$i] ? $_POST['sin_prod_vat_price'.$i] : "0";
									"<br/>".$sel_admin_id="select `admin_id` from `site_setting` where `cm_id`='".$cm_id1."' and `type`='ST'";
									$ptr_admin_id=mysql_query($sel_admin_id);
									$data_cm_id=mysql_fetch_array($ptr_admin_id);
									
									"<br/>".$sel_product_name=" select product_name,product_code,pcategory_id,sub_id,size,unit,commission,price,vender,type,added_date,cm_id,quantity,admin_id from product where product_id='".$product_id."' ";
									$ptr_names=mysql_query($sel_product_name);
									if(mysql_num_rows($ptr_names))
									{
										$data_product=mysql_fetch_array($ptr_names);
										$sele_cate="select product_id from product where product_name='".$data_product['product_name']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."' ";
										$ptr_sele_catte=mysql_query($sele_cate);
										if(mysql_num_rows($ptr_sele_catte))
										{
											$data_product_id=mysql_fetch_array($ptr_sele_catte);
											$data_record_service_inv['product_id']=$data_product_id['product_id'];
											$update_products1="update `product` set `quantity`=(quantity+".$data_record_service_inv['sin_product_qty'].") where `product_id`='".$data_record_service_inv['product_id']."' and admin_id='".$data_record_ahm['admin_id']."' and cm_id='".$cm_ids."'  ";
											$query_update=mysql_query($update_products1);
										}
										else
										{
											$sel_category="select pcategory_name from product_category where pcategory_id='".$data_product['pcategory_id']."'";
											$ptr_category=mysql_query($sel_category);
											$data_cate=mysql_fetch_array($ptr_category);
											
											$sel_subcategory1="select sub_name from product_subcategory where sub_id='".$data_product['sub_id']."'";
											$ptr_subcategory1=mysql_query($sel_subcategory1);
											$data_subcategory=mysql_fetch_array($ptr_subcategory1);
											
											$sele_cateahm="select pcategory_id from product_category where pcategory_name='".$data_cate['pcategory_name']."' and cm_id='".$cm_ids."' order by pcategory_id asc";
											$ptr_sele_ahmcatte=mysql_query($sele_cateahm);
											if(mysql_num_rows($ptr_sele_ahmcatte))
											{
												$data_ahm_cat=mysql_fetch_array($ptr_sele_ahmcatte);
												$cat_id=$data_ahm_cat['pcategory_id'];
											}
											else
											{
												$insert_cat="insert into product_category (`pcategory_name`,`added_date`,`cm_id`,`admin_id`) values('".$data_cate['pcategory_name']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_cat=mysql_query($insert_cat);
												$cat_id=mysql_insert_id();
											}
											
											$sele_subcateahm="select sub_id from product_subcategory where sub_name='".$data_subcategory['sub_name']."' and cm_id='".$cm_ids."' order by  sub_id asc";
											$ptr_sele_subcatte=mysql_query($sele_subcateahm);
											if(mysql_num_rows($ptr_sele_subcatte))
											{
												$data_ahm_subcat=mysql_fetch_array($ptr_sele_subcatte);
												$sub_cat_id=$data_ahm_subcat['sub_id'];
											}
											else
											{
												$insert_subcat="insert into product_subcategory (`sub_name`,`pcategory_id`,`cm_id`,`admin_id`) values('".$data_subcategory['sub_name']."','".$cat_id."','".$cm_ids."','".$data_record_ahm['admin_id']."')";
												$ptr_ins_subcat=mysql_query($insert_subcat);
												$sub_cat_id=mysql_insert_id();
											}											
											$inser_prod="insert into product (`product_name`,`product_code`,`pcategory_id`,`sub_id`,`size`,`unit`,`commission`,`price`,`type`,`added_date`,`cm_id`,`quantity`,`admin_id`,`status`) values ('".$data_product['product_name']."','".$data_product['product_code']."','".$cat_id."','".$sub_cat_id."','".$data_product['size']."','".$data_product['unit']."','".$data_product['commission']."','".$data_product['price']."','".$data_product['type']."','".date('Y-m-d H:i:s')."','".$cm_ids."','".$data_record_service_inv['sin_product_qty']."','".$data_record_ahm['admin_id']."','Active')";
											$ptr_mysql_prod=mysql_query($inser_prod);
											$product_ids=mysql_insert_id();
											$data_record_service_inv['product_id']=$product_ids;
										}
									}
									$data_record_service_inv['admin_id']=$data_record_ahm['admin_id'];
									$customer_service_id=$db->query_insert("inventory_product_map", $data_record_service_inv);
									
									$sel_qty="select quantity from product where product_id='".$_POST['product_id'.$i]."' ";
									$ptr_qty=mysql_query($sel_qty);
									$data_qty=mysql_fetch_array($ptr_qty);
									
								}
							}
							
							$status='paid';
							
							if($chaque_date !='')
							{
								$chaque_date_exp=explode('/', $chaque_date);
								$sep_check_date=$chaque_date_exp[2].'-'.$chaque_date_exp[1].'-'.$chaque_date_exp[0];
							}
							else
							{
								$sep_check_date='';
							}
							//===============Update Receipt no================
							$sel_recpt="select receipt_no from inventory_invoice where cm_id='".$cm_ids."' and (receipt_no IS NOT NULL and receipt_no !='') order by receipt_no desc limit 0,1";
							$ptr_recpt=mysql_query($sel_recpt);
							$data_receipt=mysql_fetch_array($ptr_recpt);
							
							$recp=explode("-",$data_receipt['receipt_no']);
							$recpt_no=intval($recp[1])+1;
							$pre=$recp[0].'-';
							$receipt_no=$pre.$recpt_no;
							//==================================================
							"<br/>".$insert_sales_invoice = " INSERT INTO `inventory_invoice` (`inventory_id`,`receipt_no`, `price`, `total_cost`, `amount1`, `payable_amount`,`remaining_amount`, `paid_type`, `bank_id`, `cheque_detail`, `chaque_date`, `credit_card_no`, `bank_ref_no`,`admin_id`, `added_date`,`status`,`cm_id`,`total_paid`) VALUES ('".$record_id."','".$receipt_no."', '".$product_price."', '".$total_price."', '".$amount1."', '".$_POST['payable_amount']."','".$_POST['remaining_amount']."', '".$payment_mode_id."','".$bank_name."', '".$chaque_no."', '".$sep_check_date."','".$credit_card_no."','".$bank_ref_no."', '".$data_record_ahm['admin_id']."','".$added_date."','".$status."','".$cm_ids."','".$_POST['payable_amount']."'); ";
							$ptr_sales_invoice = mysql_query($insert_sales_invoice);
							//============================================================================
							$sel_cust="select name,contact from vendor where vendor_id ='".$data_record_ahm['vendor_id']."'";
							$ptr_cus_name=mysql_query($sel_cust);
							$data_cust_name=mysql_fetch_array($ptr_cus_name);
							$name=$data_cust_name['name'];
							$contact=$data_cust_name['contact'];
							$mesg ="Hi ".$name." Thanks for purchasing our service";
							$sel_inq="select sms_text from previleges where privilege_id='136'";
							$ptr_inq=mysql_query($sel_inq);
							$txt_msg='';
							if(mysql_num_rows($ptr_query))
							{
								$dta_msg=mysql_fetch_array($ptr_inq);
								$txt_msg=$dta_msg['sms_text'];
							}
						}
					//=================================================================================================
					
					"<br>".$insert="INSERT INTO `log_history`(`category`, `action`, `name`, `id`, `date`, `cm_id`, `admin_id`) VALUES ('sales_product','Add','sale product','".$record_id."','".date('Y-m-d H:i:s')."','".$_SESSION['cm_id']."','".$_SESSION['admin_id']."')";
					$query=mysql_query($insert,$con1);
					
					echo '<br></br><div id="msgbox" style="width:40%;">Record added successfully</center></div> <br></br>';
					
					$sel_cust="select cust_name,mobile1 from customer where cust_id ='".$customer_id."'";
					$ptr_cus_name=mysql_query($sel_cust);
					$data_cust_name=mysql_fetch_array($ptr_cus_name);
					$name=$data_cust_name['cust_name'];
					$contact=$data_cust_name['mobile1'];
					//$mesg ="Hi ".$name." Thanks for purchasing our service";
					$sel_inq="select sms_text from previleges where privilege_id='138'";
					$ptr_inq=mysql_query($sel_inq,$con1);
					$txt_msg='';
					if(mysql_num_rows($ptr_inq))
					{
						$dta_msg=mysql_fetch_array($ptr_inq);
						$txt_msg=$dta_msg['sms_text'];
					}
					
					$address='';
					if($branch_name1=="Pune")
					{
						$address="International School of Aesthetics and Spa, 2nd Floor, The Greens,North Main Road, Koregoan Park, Pune-411001. Location:  https://bit.ly/2yOhji6";
					}
					else if($branch_name1=="Ahmedabad")
					{
						$address="International School of Aesthetics and Spa, First Floor, Zodiac Plaza,Near Nabard Flat, H.L. Comm. College Road, Navrangpura, Ahmedabad- 380 009.Tel No-:079-26300007. Location: https://bit.ly/2N28vbw";
					}
					else if($branch_name1=="ISAS PCMC")
					{
						$address="Hari A1,Next to ABS Gym, Pimple Nilakh, Pune 411027. Location: https://bit.ly/2Ke26fQ";
					}
					
					$messagessss =$txt_msg;
					$search_by = array("student_name","isas_address");
					$replace_by = array($name,$address);
					$messagessss = str_replace($search_by, $replace_by, $messagessss);
					
					$sel_sms_cnt="select * from sms_mail_configuration_map where previlege_id='138' ".$_SESSION['where']."";
					$ptr_sel_sms=mysql_query($sel_sms_cnt,$con1);
					while($data_sel_cnt=mysql_fetch_array($ptr_sel_sms))
					{
						$sel_act="select contact_phone from site_setting where admin_id='".$data_sel_cnt['staff_id']."' and type!='S' ".$_SESSION['where']."";
						$ptr_cnt=mysql_query($sel_act,$con1);
						if(mysql_num_rows($ptr_cnt))
						{
							$data_cnt=mysql_fetch_array($ptr_cnt);
							//send_sms_function($data_cnt['contact_phone'],$messagessss);
						}
					}
					if($_SESSION['type']!='S')
					{
						"<br/>".$sel_act="select contact_phone from site_setting where type='S' ";
						$ptr_cnt=mysql_query($sel_act,$con1);
						if(mysql_num_rows($ptr_cnt))
						{
							while($data_cnt=mysql_fetch_array($ptr_cnt))
							{
								//send_sms_function($data_cnt['contact_phone'],$messagessss);
							}
						}
					}
					send_sms_function($contact,$messagessss);
					//------send notification on Sale voucher--------------------
						$notification_args['reference_id'] =$record_id;
						$notification_args['on_action'] = 'product_sale';
						$notification_status = addNotifications($notification_args);
					//---------------------------------------------------------------
					?>
				   
					 <script>
					window.open('invoice_sales_product.php?record_id=<?php echo $sales_ins_id; ?>','win1','status=no,toolbar=no, scrollbars=yes,titlebar=no, menubar=no,resizable=yes,width=900, height=600,directories=no,location=no');									
					</script>
					<div id="statusChangesDiv" title="Record added"><center><br><p>Record added successfully</p></center></div>
						<script type="text/javascript">
							$(document).ready(function() {
								$( "#statusChangesDiv" ).dialog({
										modal: true,
										buttons: {
													Ok: function() { $( this ).dialog( "close" );}
												 }
								});
							});
						//setTimeout('document.location.href="manage_sales_product.php";',1000);
						</script>
					<?php
				}
			}
		}
		if($success==0)
		{
			?>
            <tr><td>
        	<form method="post" id="jqueryForm" enctype="multipart/form-data" name="jqueryForm" onSubmit="return validme()">
			<table border="0" cellspacing="15" cellpadding="0" width="100%">
			<?php
            $where_cm='';
            if($_SESSION['where']!='')
            {
                $where_cm=" and p.cm_id='".$_SESSION['cm_id']."'";
            }
            ?>
              	<tr>
                	<td colspan="3" class="orange_font">* Mandatory Fields</td>
                    <input type="hidden" name="res1" id="res1" />
                    <input type="hidden" name="res2" id="res2" />
                	<input type="hidden" name="record_id" id="record_id" value="<?php if($_REQUEST['record_id']) { echo $record_id ;} ?>"  />
         		</tr>
				<tr>
					<td width="14%">Date<span class="orange_font">*</span></td>
					<td width="74%"><?php
					if($_SESSION['type']=='S')
					{
						?>
                        <input type="text" id="added_date" style="width:200px" name="added_date" class="input_text datepicker" value="<?php if($_POST['added_date']) echo $_POST['added_date']; else if($row_record['added_date']!=''){$arrage_date= explode(' ',$row_record['added_date']);$arr_date= explode('-',$arrage_date[0]); echo $arr_date[2].'/'.$arr_date[1].'/'.$arr_date[0];}else{echo date("d/m/Y");}?>" />
                        <?php
					}
					else
					{
						if($_POST['added_date']) echo $_POST['added_date']; else if($row_record['added_date']!=''){$arrage_date= explode(' ',$row_record['added_date']);$arr_date= explode('-',$arrage_date[0]); echo $arr_date[2].'/'.$arr_date[1].'/'.$arr_date[0];}else{echo date("d/m/Y");}?>
						<input type="hidden" id="added_date" style="width:200px" name="added_date" class="input_text datepicker" value="<?php if($_POST['added_date']) echo $_POST['added_date']; else if($row_record['added_date']!=''){$arrage_date= explode(' ',$row_record['added_date']);$arr_date= explode('-',$arrage_date[0]); echo $arr_date[2].'/'.$arr_date[1].'/'.$arr_date[0];}else{echo date("d/m/Y");}?>" />
						<?php
					}
					?>
					</td>
				</tr> 
              	<?php 
				if($_SESSION['type']=='S' || $_SESSION['type']=='Z' || $_SESSION['type']=='LD' )
				{
					?>
					<tr>
						<td>Select Branch</td>
						<td>
						<?php 
						if($_REQUEST['record_id'])
						{
							$sel_cm_id="select branch_name from site_setting where cm_id=".$row_record['cm_id']." and type='A'";
							$ptr_query=mysql_query($sel_cm_id);
							$data_branch_nmae=mysql_fetch_array($ptr_query);
						}
						$sel_branch = "SELECT * FROM branch where 1 order by branch_id asc ";	 
						$query_branch = mysql_query($sel_branch);
						$total_Branch = mysql_num_rows($query_branch);
						echo'<table width="100%"><tr><td>'; 
						echo'<select id="branch_name" name="branch_name" onchange="show_bank(this.value)" style="width:200px">';
						while($row_branch = mysql_fetch_array($query_branch))
						{
							?>
							<option value="<?php echo $row_branch['branch_name'];?>" <?php if ($_POST['branch_name'] ==$row_branch['branch_name']) echo 'selected="selected"'; else if($row_branch['branch_name']==$data_branch_nmae['branch_name']) echo 'selected="selected"'?> /><?php echo $row_branch['branch_name']; ?> 

							</option>
							<?php
						}
						echo '</select>';
						echo "</td></tr></table>";
						?>
						</td>
					</tr>
					<?php 
				}
				else 
				{
					?>
                    <input type="hidden" name="branch_name" id="branch_name" value="<?php echo $_SESSION['branch_name'];?>"/> 
                    <?php
				}?>
                <tr>
                	<td width="14%" valign="top">Referal invoice No.<span class="orange_font">*</span></td>
                	<td width="74%"><input type="text"  class="input_text" style="width:200px" name="ref_invoice_no" id="ref_invoice_no" value="<?php if($_POST['save_changes']) echo $_POST['ref_invoice_no']; else if($row_record['ref_invoice_no'] !='') echo $row_record['ref_invoice_no']; ?>" /></td> 
                	<td width="12%"></td>
          		</tr>
                <tr>
                	<td width="14%" valign="top">Show <?php if($_SESSION['tax_type']=='GST') echo 'GST'; else echo 'VAT'; ?><span class="orange_font">*</span></td>
                	<td width="74%"><input type="radio" class="input_radio" name="show_gst" id="show_gst" value="yes" <?php if($_POST['show_gst']=='yes') echo 'checked="checked"'; else if($row_record['show_gst']=='yes') echo 'checked="checked"'; else echo 'checked="checked"';  ?> />Yes &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" class="input_radio" name="show_gst" id="show_gst" value="no" <?php if($_POST['show_gst']=='no') echo 'checked="checked"'; else if($row_record['show_gst']=='no') echo 'checked="checked"'; ?>/>No</td> 
                	<td width="12%"></td>
          		</tr>
				<tr>           		
            		<td width="14%" >Select Type<span class="orange_font">*</span></td>
            		<td>
                	<select id="user" name="user" onChange="show_data(this.value)" style="width:200px">
                	<option value="">Select</option>
					<option value="Student" <?php if($row_record['type']=="Student") echo 'selected="selected"'; ?>>Student</option>
					<option value="Customer" <?php if($row_record['type']=="Customer") echo 'selected="selected"'; ?>>Customer</option>
					<option value="Employee" <?php if($row_record['type']=="Employee") echo 'selected="selected"'; ?>>Employee</option>
                	</select>
                	</td>
            	</tr> 
				<tr>
					<td colspan="3">
						<div id="show_type">
						</div>
					</td>
            	</tr>
				<tr>
					<td colspan="3">
					<div id='stockiest'></div>
					</td>
				</tr>
                <tr>
                    <td width="14%" class="tr-header" >Biling Address</td>
                    <td><textarea name="billing_address" id="billing_address" class="input_text"  style="width:90%;height:80px" ><?php if($_POST['billing_address']) echo $_POST['billing_address']; else echo $row_record['billing_address']; ?></textarea></td>
                </tr>
                
              	<tr>
                	<td width="14%" valign="top">Delivery Address</td>
                	<td width="74%"><textarea name="delivery_address" id="delivery_address" class="input_text" style="width:90%; height:80px"><?php if($_POST['delivery_address']) echo $_POST['delivery_address']; else echo $row_record['delivery_address'];?></textarea></td>
              	</tr>
            	<tr>
            		<!--<td width="10%">Select Product<span class="orange_font">*</span></td>-->
            		<td colspan="3">
                	<table  width="100%" style="border:1px solid gray; ">
                    	<tr>
                    	<td colspan="3">
                    	<!--==================================NEW TABLE START===========================-->
						<table cellpadding="5" width="100%" >
						<tr>
                        <?php
						if($record_id =='')
						{
							?>
							<input type="hidden" name="no_of_floor" id="no_of_floor" class="inputText" size="1" onKeyUp="create_floor();" value="0" />
                        	<?php 
					 	}?>
						<script language="javascript">
						<?php if($_SESSION['tax_type']=='GST')
						{
							?>
							function floors(idss)
							{
								res= document.getElementById("res1").value;
								res11= document.getElementById("res2").value;
								
								var shows_data='<div id="floor_id'+idss+'"><table cellspacing="3" id="tbl'+idss+'" width="100%"><tr><td width="20%" align="center"><input type="hidden" name="exclusive_id'+idss+'" id="exclusive_id'+idss+'" value="'+idss+'" /><select name="product_id'+idss+'" id="product_id'+idss+'" style="width:219px" onchange="show_product_qty(this.value,'+idss+')"><option value="">Select Product</option>'+res+'<?php
								/*$sel_tel = "select p.product_id,p.product_name,p.price,p.quantity,p.admin_id from product p, site_setting s where 1 and p.status='Active' and p.admin_id=s.admin_id ".$where_cm." and quantity > 0  order by product_id asc";	 //remove ".$_SESSION['user_id']." on 14/5/2018 --and s.type='ST'  on 13/6/18
								$query_tel = mysql_query($sel_tel);
								while($data=mysql_fetch_array($query_tel))
								{
									$name='';
									$sel_emp="select name from site_setting where admin_id='".$data['admin_id']."'";
									$ptr_admin_id=mysql_query($sel_emp);
									$data_name=mysql_fetch_array($ptr_admin_id);
									$name= "(".$data_name['name'].")";
									echo '<option value="'.$data['product_id'].'">'.addslashes($data['product_name']).'&nbsp;&nbsp;(Price - '.addslashes($data['price']).')&nbsp;&nbsp;'.$name.'</option>';
								}*/
								 ?>
								 </select></td><td width="3%" align="center"><input type="text" name="prod_price'+idss+'" id="prod_price'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:60px" /></td><td width="3%" align="center"><input type="text" name="prod_base_price'+idss+'" id="prod_base_price'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:60px" /></td><td width="2%" align="center"><input type="text" name="product_total_qty'+idss+'" id="product_total_qty'+idss+'" style="width:50px;" /></td><td width="4%" align="center"><input type="text" name="product_disc'+idss+'" id="product_disc'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:90px" /></td><td width="4%" align="center"><input type="text" name="prod_discounted_price'+idss+'" id="prod_discounted_price'+idss+'" style="width:50px" /><input type="hidden" name="prod_disc_price'+idss+'" id="prod_disc_price'+idss+'" /></td><td width="4%" align="center"><input type="text" name="product_qty'+idss+'" id="product_qty'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:50px" /></td><td width="5%" align="center"><input type="text" onkeyup="calc_product_price('+idss+')" name="sin_product_cgst'+idss+'" id="sin_product_cgst'+idss+'" style=" width:60px"><input type="text" name="sin_prod_cgst_price'+idss+'" id="sin_prod_cgst_price'+idss+'" style=" width:60px" /></td><td width="5%" align="center"><input type="text" name="sin_product_sgst'+idss+'" onkeyup="calc_product_price('+idss+')" id="sin_product_sgst'+idss+'" style=" width:60px"><input type="text" name="sin_prod_sgst_price'+idss+'" id="sin_prod_sgst_price'+idss+'" style=" width:60px" /></td><td width="5%" align="center"><input type="text" name="sin_product_igst'+idss+'" onkeyup="calc_product_price('+idss+')" id="sin_product_igst'+idss+'" style=" width:60px"><input type="text" name="sin_prod_igst_price'+idss+'" id="sin_prod_igst_price'+idss+'" style=" width:60px" /></td><td width="4%" align="center"><input type="text" name="mrp_price'+idss+'" id="mrp_price'+idss+'" readonly="readonly" style="width:50px" /></td><td width="4%" align="center"><input type="text" name="sales_product_price'+idss+'" id="sales_product_price'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:50px" /><input type="hidden" name="total_sales_product[]" id="total_sales_product'+idss+'" /><input type="hidden" name="row_deleted'+idss+'" id="row_deleted'+idss+'" value="" /></td><td width="10%"><select name="staff_id'+idss+'" id="staff_id'+idss+'" style="width:100px"><option value="">Select Staff</option>'+res11+'<?php 
										/*if($_SESSION['type']=="S" || $_SESSION['type']=='Z' || $_SESSION['type']=='LD' )
										{
											$sel_staff = "select admin_id,name from site_setting where 1 and system_status='Enabled' ".$_SESSION['where']." order by admin_id asc";	 
											$query_staff = mysql_query($sel_staff);
											if($total_staff=mysql_num_rows($query_staff))
											{
												while($data=mysql_fetch_array($query_staff))
												{
													echo '<option value="'.$data['admin_id'].'">'.$data['name'].'</option>';
												}
											}
										}
										else
										{
											$sel_prev_id="select DISTINCT(admin_id) from staff_previleges where 1 ".$prev_value." ".$_SESSION['where']."  ";
											$ptr_id=mysql_query($sel_prev_id);
											if(mysql_num_rows($ptr_id))
											{
												while($data_prev_id=mysql_fetch_array($ptr_id))
												{
													$sel_staff = "select admin_id,name from site_setting where 1 and admin_id='".$data_prev_id['admin_id']."' and system_status='Enabled' ".$_SESSION['where']." order by admin_id asc";	
													$query_staff = mysql_query($sel_staff);
													if(mysql_num_rows($query_staff))
													{
														$data=mysql_fetch_array($query_staff);
														echo '<option value="'.$data['admin_id'].'">'.$data['name'].'</option>';
													}
												}
											}
										}*/
										?>
										</select></td><td width="2%" align="center"><a onClick="reset_price('+idss+')"><img src="images/refresh.png" height="25" width="25" alt="refresh"></a></td><input type="hidden" name="base_price'+idss+'" id="base_price'+idss+'" value="" /><tr><tr><td colspan="15"><div style="display:none" id="product_details'+idss+'"><table style="width:100%" align="center"><tr><td width="10%">Price : <span id="price_desc'+idss+'"></span></td><td width="10%">Brand : <span id="brand'+idss+'"></span></td><td width="10%">Unit : <span id="unit_desc'+idss+'"></span><span id="measure_desc'+idss+'"></span></td><td width="70%">Product Desc.: <span id="description_desc'+idss+'"></span></td></tr></table></div></td></tr></table></div>';
								document.getElementById('floor').value=idss;
								return shows_data;
							}
							<?php
						}
						else
						{
							?>
							function floors(idss)
							{
								res= document.getElementById("res1").value;
								res11= document.getElementById("res2").value;
								
								var shows_data='<div id="floor_id'+idss+'"><table cellspacing="3" id="tbl'+idss+'" width="100%"><tr><td width="11%" align="center"><input type="hidden" name="exclusive_id'+idss+'" id="exclusive_id'+idss+'" value="'+idss+'" /><select name="product_id'+idss+'" id="product_id'+idss+'" style="width:219px" onchange="show_product_qty(this.value,'+idss+')"><option value="">Select Product</option>'+res+'<?php
								/*$sel_tel = "select p.product_id,p.product_name,p.price,p.quantity,p.admin_id from product p, site_setting s where 1 and p.status='Active' and p.admin_id=s.admin_id ".$where_cm." and quantity > 0  order by product_id asc";	 //remove ".$_SESSION['user_id']." on 14/5/2018 --and s.type='ST'  on 13/6/18
								$query_tel = mysql_query($sel_tel);
								while($data=mysql_fetch_array($query_tel))
								{
									$name='';
									//if($_SESSION['type'] =='S')
									//{
										$sel_emp="select name from site_setting where admin_id='".$data['admin_id']."'";
										$ptr_admin_id=mysql_query($sel_emp);
										$data_name=mysql_fetch_array($ptr_admin_id);
										$name= "(".$data_name['name'].")";
									//}
																																																					echo '<option value="'.$data['product_id'].'">'.addslashes($data['product_name']).'&nbsp;&nbsp;(Price - '.addslashes($data['price']).')&nbsp;&nbsp;'.$name.'</option>';
								}*/
								 ?>
								 </select></td><td width="3%" align="center"><input type="text" name="prod_price'+idss+'" id="prod_price'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:60px" /></td><td width="3%" align="center"><input type="text" name="prod_base_price'+idss+'" id="prod_base_price'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:60px" /></td><td width="2%" align="center"><input type="text" name="product_total_qty'+idss+'" id="product_total_qty'+idss+'" style="width:50px;" /></td><td width="4%" align="center"><input type="text" name="product_disc'+idss+'" id="product_disc'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:90px" /></td><td width="4%" align="center"><input type="text" name="prod_discounted_price'+idss+'" id="prod_discounted_price'+idss+'" style="width:50px" /><input type="hidden" name="prod_disc_price'+idss+'" id="prod_disc_price'+idss+'" /></td><td width="3%" align="center"><input type="text" name="product_qty'+idss+'" id="product_qty'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:50px" /></td><td width="5%" align="center"><input type="text" onkeyup="calc_product_price('+idss+')" name="sin_product_vat'+idss+'" id="sin_product_vat'+idss+'" style=" width:60px"><input type="text" name="sin_prod_vat_price'+idss+'" id="sin_prod_vat_price'+idss+'" style=" width:60px" /><input type="hidden" onkeyup="calc_product_price('+idss+')" name="sin_product_cgst'+idss+'" id="sin_product_cgst'+idss+'" style=" width:60px"><input type="hidden" name="sin_prod_cgst_price'+idss+'" id="sin_prod_cgst_price'+idss+'" style=" width:60px" /><input type="hidden" name="sin_product_sgst'+idss+'" onkeyup="calc_product_price('+idss+')" id="sin_product_sgst'+idss+'" style=" width:60px"><input type="hidden" name="sin_prod_sgst_price'+idss+'" id="sin_prod_sgst_price'+idss+'" style=" width:60px" /><input type="hidden" name="sin_product_igst'+idss+'" onkeyup="calc_product_price('+idss+')" id="sin_product_igst'+idss+'" style=" width:60px"><input type="hidden" name="sin_prod_igst_price'+idss+'" id="sin_prod_igst_price'+idss+'" style=" width:60px" /></td><td width="4%" align="center"><input type="text" name="mrp_price'+idss+'" id="mrp_price'+idss+'" readonly="readonly" style="width:50px" /></td><td width="4%" align="center"><input type="text" name="sales_product_price'+idss+'" id="sales_product_price'+idss+'" onkeyup="calc_product_price('+idss+')" style="width:50px" /><input type="hidden" name="total_sales_product[]" id="total_sales_product'+idss+'" /><input type="hidden" name="row_deleted'+idss+'" id="row_deleted'+idss+'" value="" /></td><td width="10%"><select name="staff_id'+idss+'" id="staff_id'+idss+'" style="width:100px"><option value="">Select Staff</option>'+res11+'<?php /*if($_SESSION['type']=="S" || $_SESSION['type']=='Z' || $_SESSION['type']=='LD' )
								 {
									$sel_staff = "select admin_id,name from site_setting where 1 and system_status='Enabled' ".$_SESSION['where']." order by admin_id asc";
									$query_staff = mysql_query($sel_staff);
									if($total_staff=mysql_num_rows($query_staff))
									{
										while($data=mysql_fetch_array($query_staff))
										{
											echo '<option value="'.$data['admin_id'].'">'.$data['name'].'</option>';
										}
									}
									}
									else
									{
										$sel_prev_id="select DISTINCT(admin_id) from staff_previleges where 1 ".$prev_value." ".$_SESSION['where']."  ";
										$ptr_id=mysql_query($sel_prev_id);
										if(mysql_num_rows($ptr_id))
										{
											while($data_prev_id=mysql_fetch_array($ptr_id))
											{
												$sel_staff = "select admin_id,name from site_setting where 1 and admin_id='".$data_prev_id['admin_id']."' and system_status='Enabled' ".$_SESSION['where']." order by admin_id asc";	
												$query_staff = mysql_query($sel_staff);
												if(mysql_num_rows($query_staff))
												{
													$data=mysql_fetch_array($query_staff);
													echo '<option value="'.$data['admin_id'].'">'.$data['name'].'</option>';
												}
											}
										}
									}*/
									?>
										</select></td><td width="2%" align="center"><a onClick="reset_price('+idss+')"><img src="images/refresh.png" height="25" width="25" alt="refresh"></a></td><input type="hidden" name="base_price'+idss+'" id="base_price'+idss+'" value="" /><tr><tr><td colspan="15"><div style="display:none" id="product_details'+idss+'"><table style="width:100%" align="center"><tr><td width="10%">Price : <span id="price_desc'+idss+'"></span></td><td width="10%">Brand : <span id="brand'+idss+'"></span></td><td width="10%">Unit : <span id="unit_desc'+idss+'"></span><span id="measure_desc'+idss+'"></span></td><td width="70%">Product Desc.: <span id="description_desc'+idss+'"></span></td></tr></table></div></td></tr></table></div>';
								document.getElementById('floor').value=idss;
								return shows_data;
							}
							<?php
						}
						?>
                        </script>
						<td align="right"><input type="button"  name="Add" class="addBtn" onClick="javascript:create_floor('add');" alt="Add(+)" ><input type="button" name="Add"  class="delBtn"  onClick="javascript:create_floor('delete');" alt="Delete(-)" ></td></tr>
                        <tr><td></td><td align="left"></td></tr>
                    </table> 
                    <table width="100%" border="0" class="tbl" bgcolor="#CCCCCC" align="center"><tr><td align="center"></td><td align="center"></td><td align="center"></td></tr> <tr><td align="center" width="25%"> </td><td width="10%"> </td><td width="5%"> </td></tr>
					<tr>
                        <td colspan="6">
							<table cellspacing="3" id="tbl" width="100%">
							<tr>
								<td valign="top" align="center" width="21%">Product Name</td>
								<td valign="top" align="center" width="6%">MRP</td>
								<td valign="top" align="center" width="6%">Base Price</td>
								<td valign="top" align="center" width="6%">Total Qty in Stock</td>
								<td valign="top" align="center" width="8%">Discount<br/>
								<input type="radio" name="discount1" id="discount1" checked="checked" value="percentage" <?php if($_POST['discount1']=="percentage") {echo 'checked="checked"';}else if($data_map['disc_type']=="percentage") { echo 'checked="checked"';} ?> />in %
								<input type="radio" name="discount1" id="discount1" value="rupees" <?php if($_POST['discount1']=="rupees") {echo 'checked="checked"';}else if($data_map['disc_type']=="rupees") { echo 'checked="checked"';} ?> />in <?php if($_SESSION['tax_type']=='GST') echo 'Rs'; else echo 'AED'; ?></td>
								<td valign="top" width="6%" align="center">Discounted price</td>
								<td valign="top" width="5%" align="center">Qty</td>
                                <?php
								if($_SESSION['tax_type']=='GST')
								{
									?>
									<td valign="top" width="6%" align="center">CGST</td>
									<td valign="top" width="6%" align="center">SGST</td>
									<td valign="top" width="6%" align="center">IGST</td>
									<?php
								}
								else
								{
									?>
                                    <td valign="top" width="6%" align="center">VAT</td>
                                    <?php
								}
								?>
								<td valign="top" width="6%" align="center" >MRP</td>
								<td valign="top" width="5%" align="center">Total</td>
								<td valign="top" width="8%"  align="center">Referral Staff</td>
										
								<td valign="top" width="6%" align="center">Reset</td>
							</tr>
                            <tr>
                                <td colspan="15">
                                <?php
                                if($record_id!='')
                                {
                                    $select_exc = "select * from sales_product_map where sales_product_id='".$record_id."' and product_id!='' order by map_id asc ";
                                    $ptr_fs = mysql_query($select_exc);
                                    $t=1;
                                    $total_comision= mysql_num_rows($ptr_fs);
                                    $total_conditions= mysql_num_rows($ptr_fs);
                                    while($data_exclusive = mysql_fetch_array($ptr_fs))
                                    { 
                                        $slab_id= $data_exclusive['map_id'];
                                        ?> 
                                        <div class="floor_div" id="floor_id<?php echo $t; ?>">
                                        <table cellspacing="5" id="tbl<?php echo $t; ?>" width="100%">
                                            <tr>
                                                <td width="10%" align="center">
                                                <select name="product_id<?php echo $t; ?>" id="product_id<?php echo $t; ?>" style="width:200px" onChange="show_product_qty(this.value,<?php echo $t; ?>)"><option value="">Select Product</option><?php
                                                    echo $sel_tel = "select p.product_id,p.product_name,p.price,p.quantity,p.admin_id from product p, site_setting s where 1 and p.status='Active' and p.admin_id=s.admin_id ".$where_cm." and quantity > 0  order by product_id asc";//remove ".$_SESSION['user_id']." on 14/5/2018 --and s.type='ST' on 13/6/18
                                                    $query_tel = mysql_query($sel_tel);
                                                    $qty_in_stock='';
                                                    while($data_prod=mysql_fetch_array($query_tel))
                                                    {
                                                        $name='';
                                                        $sel='';
                                                        echo $data_exclusive['product_id'];
                                                        if($data_exclusive['product_id']==$data_prod['product_id'])
                                                        {
                                                            $sel='selected="selected"';
                                                            $qty_in_stock=$data['quantity'];
                                                        }
                                                        $sel_emp="select name from site_setting where admin_id='".$data['admin_id']."'";
                                                        $ptr_admin_id=mysql_query($sel_emp);
                                                        $data_name=mysql_fetch_array($ptr_admin_id);
                                                        $name= "(".$data_name['name'].")";
                                                        echo '<option value="'.$data_prod['product_id'].'" '.$sel.'>'.addslashes($data_prod['product_name']).'&nbsp;&nbsp;(Price - '.addslashes($data_prod['price']).')&nbsp;&nbsp;'.$name.'</option>';
                                                    }
                                                    
                                                ?>
													</select>
													</td>
                                                                                                       
													<td width="6%" align="center"><input type="text" name="prod_price<?php echo $t; ?>" id="prod_price<?php echo $t; ?>" style=" width:60px" value="<?php echo $data_exclusive['prod_price'] ?>" onkeyup="calc_product_price(<?php echo $t; ?>)" /></td>
													<td width="6%" align="center"><input type="text" name="prod_base_price<?php echo $t; ?>" id="prod_base_price<?php echo $t; ?>" style=" width:60px;background-color:#cccc" readOnly value="<?php echo $data_exclusive['base_prod_price'] ?>" onkeyup="calc_product_price(<?php echo $t; ?>)" /></td>
													<td width="5%" align="center"><input type="text"name="product_total_qty<?php echo $t; ?>" id="product_total_qty<?php echo $t; ?>" style=" width:40px" value="<?php echo $qty_in_stock; ?>" onKeyUp="getDiscount(<?php echo $t; ?>)"/></td>
													<td width="8%" align="center"><input type="text" name="product_disc<?php echo $t; ?>" id="product_disc<?php echo $t; ?>" value="<?php echo $data_exclusive['product_disc']; ?>" onKeyUp="calc_product_price(<?php echo $t; ?>)"  style=" width:70px" /></td>
													
													<td width="4%" align="center"><input type="text" name="prod_discounted_price<?php echo $t; ?>" id="prod_discounted_price<?php echo $t; ?>" style="width:60px" value="<?php echo $data_exclusive['discounted_price'] ?>" /><input type="hidden" name="prod_disc_price<?php echo $t; ?>" id="prod_disc_price<?php echo $t; ?>" value="<?php echo $data_exclusive['discounted_price'] ?>" /></td><td width="4%" align="center"><input type="text" name="product_qty<?php echo $t; ?>" id="product_qty<?php echo $t; ?>" onkeyup="calc_product_price(<?php echo $t; ?>)" style="width:50px" value="<?php echo $data_exclusive['product_qty'] ?>" /></td>
                                                    <?php
													if($_SESSION['tax_type']=='GST')
													{
														?>
                                                        <td width="5%" align="center"><input type="text" onkeyup="calc_product_price(<?php echo $t; ?>)" name="sin_product_cgst<?php echo $t; ?>" id="sin_product_cgst<?php echo $t; ?>" style=" width:40px" value="<?php echo $data_exclusive['cgst_tax_in_per'] ?>" /><input type="text" name="sin_prod_cgst_price<?php echo $t; ?>" style=" width:40px" id="sin_prod_cgst_price<?php echo $t; ?>" value="<?php echo $data_exclusive['cgst_tax'] ?>" /></td><td width="5%" align="center"><input type="text" name="sin_product_sgst<?php echo $t; ?>" onkeyup="calc_product_price(<?php echo $t; ?>)" id="sin_product_sgst<?php echo $t; ?>" style=" width:40px" value="<?php echo $data_exclusive['sgst_tax_in_per'] ?>" /><input type="text" name="sin_prod_sgst_price<?php echo $t; ?>" style=" width:40px" id="sin_prod_sgst_price<?php echo $t; ?>" value="<?php echo $data_exclusive['sgst_tax'] ?>" /></td><td width="5%" align="center"><input type="text" name="sin_product_igst<?php echo $t; ?>" onkeyup="calc_product_price(<?php echo $t; ?>)" id="sin_product_igst<?php echo $t; ?>" style=" width:40px" value="<?php echo $data_exclusive['igst_tax_in_per'] ?>" /><input type="text" name="sin_prod_igst_price<?php echo $t; ?>" id="sin_prod_igst_price<?php echo $t; ?>" style=" width:40px" value="<?php echo $data_exclusive['igst_tax'] ?>" /></td>
                                                        <?php
													}
													else
													{
														?>
                                                        <td width="5%" align="center"><input type="text" onkeyup="calc_product_price(<?php echo $t; ?>)" name="sin_product_vat<?php echo $t; ?>" id="sin_product_vat<?php echo $t; ?>" style=" width:40px" value="<?php echo $data_exclusive['service_tax_in_per'] ?>" /><input type="text" name="sin_prod_vat_price<?php echo $t; ?>" style=" width:40px" id="sin_prod_vat_price<?php echo $t; ?>" value="<?php echo $data_exclusive['service_tax'] ?>" /></td>
                                                        <?php
													}
													?>
                                                    <td width="4%" align="center"><input type="text" name="mrp_price<?php echo $t; ?>" id="mrp_price<?php echo $t; ?>" readonly="readonly" style="width:60px" value="<?php echo $data_exclusive['prod_price'] ?>" /></td><td width="6%" align="center"><input type="text"  name="sales_product_price<?php echo $t; ?>" id="sales_product_price<?php echo $t; ?>" style=" width:60px" value="<?php echo $data_exclusive['sales_product_price'] ?>"></td>
													<td width="10%">
													<select name="staff_id<?php echo $t; ?>" id="staff_id<?php echo $t; ?>" style="width:100px">
													<option value="">Select Staff</option>
													<?php
													$sel_staff ="select admin_id,name from site_setting where 1 and system_status='Enabled' ".$_SESSION['where']." order by admin_id asc";	 
													$query_staff = mysql_query($sel_staff);
													if($total_staff=mysql_num_rows($query_staff))
													{
														while($data=mysql_fetch_array($query_staff))
														{
															$selected='';
															if($data_exclusive['admin_id']==$data['admin_id'])
															{
																$selected='selected="selected"';
															}
															echo '<option value="'.$data['admin_id'].'" '.$selected.'>'.$data['name'].'</option>';
														}
													}
													?>
													</select>
													</td>
													<td width="2%" align="center"><a onClick="reset_price(<?php echo $t; ?>)"><img src="images/refresh.png" height="25" width="25" alt="refresh"></a><input type="hidden" name="base_price<?php echo $t; ?>" id="base_price<?php echo $t; ?>" value="<?php if($record_id!='') echo '0'; ?>" /><br/>
													<?php
													if($record_id)
													{
														?>
														<input type="hidden" name="total_product[]" id="total_product<?php echo $t; ?>" />
														<input type="hidden" name="floor_id<?php echo $t; ?>" id="floor_id_<?php echo $t; ?>" value="<?php echo $data_exclusive['map_id'] ?>" />
														<input type="button" title="Delete Options(-)" onClick="delete_product(<?php echo $t; ?>,'floor');" class="delBtn" name="del">
														<input type="hidden" name="del_floor<?php echo $t; ?>" id="del_floor<?php echo $t; ?>" value="" />
														</td>
														
														<?php
													}
													?>   												
												</tr>
											</table>
											</div>
											<script>
											$("#product_id<?php echo $t; ?>").chosen({allow_single_deselect:true});
											$("#staff_id<?php echo $t; ?>").chosen({allow_single_deselect:true});
											</script>
											<?php
											$t++;
                         				}
									}
                       				?>
                                    </td>
                        		</tr> 
							</table>
							<input type="hidden" name="floor" id="floor" value="0" />
                        <div id="create_floor"></div>
						</td>
					</tr>
					</table>
                    <!--=====================================END TABLE====================================-->
                    </td>
                    </tr>
                </table>
            </td>
            </tr>
           <!-- <td width="90%" >
           <?php /*?> <?php
            $sel_tel = "select product_id,product_name,price,quantity from product order by product_id asc";	 
			$query_tel = mysql_query($sel_tel);
			$i=1;
			$total_no = mysql_num_rows($query_tel);
			$member_result='';
			echo '<table width="100%">';
			
			echo  '<tr>';
			///-======= Existing course code===
			
			if($record_id)
			{ 
				$select_existing = " select product_id, sales_product_id, quantity from sales_product_map where sales_product_id='".$record_id."' ";
				$ptr_esxit = mysql_query($select_existing);
				$subject_array = array();
				$topic_array = array();
				$j=0;
				while( $data_exist = mysql_fetch_array($ptr_esxit))
				{
					$customer_array[$j]=$data_exist['sales_product_id'];
					$service_array[$j]=$data_exist['product_id'];
					$j++;
				}
			}
			while($row_member = mysql_fetch_array($query_tel))
			   {
				   $checked= '';
				if($record_id)
				{
					if(in_array($row_member['product_id'], $service_array))
					{
						$checked=" checked='checked'";
					}
				}
				   //$checked= '';
				  $select_product_chk= "select product_id, sales_product_id, quantity from sales_product_map where sales_product_id='".$row_record['sales_product_id']."' and product_id='".$row_member['product_id']."' ";
				   $ptr_product_chk=mysql_query($select_product_chk);
				   $fetch_product_ck=mysql_fetch_array($ptr_product_chk);
				   if(mysql_num_rows($ptr_product_chk))
				   {
					   //$checked= 'checked="checked"';
					   
					   $valu_quantity=$fetch_product_ck['quantity'];
				   }
				   else
				   {
					  $valu_quantity=$row_member['quantity']; 
				   }
				   
			   echo  '<td style="border:1px solid #999;">'; 
			  
			  
			   
			   echo  "<input type='checkbox' '".$checked."' name='requirment_id[]'  value='".$row_member['product_id']."' id='requirment_id$i'  onClick='showprice()' class='case' $checked /> ".$row_member['product_name']."( Price - ".$row_member['price']."/- )(Qty - ".$row_member['quantity'].")"." ";
			   
			   echo '<input type="hidden" name="price_hidden" value="'.$row_member['price'].'" id="price_hidden'.$i.'" />';
			   
			     echo '<input type="hidden" name="quantity_total[]" value="'.$row_member['quantity'].'" id="quantity_total'.$i.'" />';
			   
			   echo' &emsp; Qty: <input type="text" name="quantity[]" value="" id="quantity'.$i.'" style="width:30px" onkeyup="showprice()"/>';
			   
			  
			  
			   /*if($record_id!='' && (mysql_num_rows($ptr_product_chk)))
			   {
				   $tot=$row_member['price'] * $valu_quantity;
				   
				   echo '<div id="tot_pr_'.$i.'">';
				   echo' &emsp; Total: <input type="text" name="total_price" value="'.$tot.'" id="total_price'.$i.'" readonly="readonly" style="width:60px" />';
				   echo '</div>';
			   }
			  
			  
			  
			   
			   echo' &emsp; Total: <input type="text" name="total_price" value="" id="total_price'.$i.'" readonly="readonly" style="width:50px" />';
			 
			   
			 
			  
			   echo  '</td>';
			   if($i%2==0)
			   echo  '<tr></tr>';  
			   $i++;
				}
				echo' <input type="hidden" name="total_product" value="'.($i-1).'" id="total_product" />';
				echo '</table>';
            
            ?><?php */?>
                                       
                    </td> -->
           <!--</tr> -->
            <tr>
                <td width="14%" valign="top">Product Price<span class="orange_font">*</span></td>
                <td width="74%"><input type="text" class="input_text" name="product_price" style="width:200px" id="product_price" onKeyUp="calculte_total_cost()" value="<?php if($_POST['product_price']) echo $_POST['product_price']; else echo $row_record['product_price'];?>" /></td> 
                <td width="12%"><input type="hidden" name="total_prod_discounted_price" id="total_prod_discounted_price" value="" /></td>
            </tr>
             
            <tr>
                <td width="14%" valign="top" style="display:none">Discount <input type="radio" name="discount_type"  onChange="calculte_total_cost()" id="discount_type" checked="checked" value="percentage" <?php if($_POST['discount_type']=="percentage") {echo 'checked="checked"';}else if($row_record['discount_type']=="percentage") { echo 'checked="checked"';} ?>  />in %
                <input type="radio" name="discount_type" id="discount_type" onChange="calculte_total_cost()" value="rupees" <?php if($_POST['discount_type']=="rupees") {echo 'checked="checked"';}else if($row_record['discount_type']=="rupees") { echo 'checked="checked"';} ?> />in <?php if($_SESSION['tax_type']=='GST') echo 'Rs -/'; else echo 'AED'; ?> <span class="orange_font">*</span></td>
                <td width="74%" style="display:none"><input type="text"  class="input_text" name="discount" style="width:200px" id="discount" onKeyUp="calculte_total_cost()" value="<?php if($_POST['discount']) echo $_POST['discount']; else echo $row_record['discount'];?>" /></td> 
                <td width="12%" style="display:none"></td>
            </tr>
            <tr>
            	<td width="14%" valign="top" style="display:none">Discount Price</td>
            	<td width="74%" style="display:none"><input type="text" class=" input_text" style="width:200px" name="discount_price" id="discount_price" value="<?php if($_POST['discount_price']) echo $_POST['discount_price']; else echo $row_record['discount_price'];?>" /></td> 
            	<td width="12%"><input type="hidden"  class="input_text" style="width:200px" name="total_price" id="total_price" value="<?php if($_POST['total_price']) echo $_POST['total_price']; else echo $row_record['total_price'];?>" /></td>
            </tr>
            <!--<tr>
                <td width="20%" valign="top">Total Price</td>
                <td width="70%"><input type="text"  class="input_text" name="total_price" id="total_price" value="<?php //if($_POST['total_price']) echo $_POST['total_price']; else echo $row_record['total_price'];?>" /></td> 
                <td width="10%"></td>
            </tr>-->
            <!--===========================================================NEW TABLE 2 START===================================-->   
               <!--<tr>
                    <td width="10%">Tax<span class="orange_font">*</span></td>    
                    <td colspan="2">
                    <table  width="100%" style="border:1px solid gray; ">
                        <tr>
                        <td colspan="2">
                        <table cellpadding="5" width="100%" >
                         <tr>
                         <?php
                         /*if($record_id =='')
                         {*/
                            ?>
                            <input type="hidden" name="type1" id="type1" class="inputText" size="1" onKeyUp="create_type1();" value="0" />
                            <?php 
                         //}?>
                        <script language="javascript">
                            function type1(idss)
                            {
                                res_tax= document.getElementById("res_tax").value;
                                //alert(idss);
                                var shows_data='<div id="type1_id'+idss+'"><table cellspacing="3" id="tbl'+idss+'" width="100%"><tr><td valign="top" width="8%"><input type="hidden" name="exclusive_id'+idss+'" id="exclusive_id'+idss+'" value="" /><select name="tax_type'+idss+'" id="tax_type'+idss+'" onChange="get_tax_value('+idss+',this.value)"><option value="">Select Tax</option>'+res_tax+'</select></td><td valign="top" width="8%" align="left"><input type="text" name="tax_value'+idss+'" id="tax_value'+idss+'" onKeyUp="calculte_amount_tax('+idss+')"/><input type="hidden" name="tax_amount'+idss+'" id="tax_amount'+idss+'" /></td><input type="hidden" name="total_tax[]" id="total_tax'+idss+'" /><input type="hidden" name="row_deleted'+idss+'" id="row_deleted'+idss+'" value="" /><tr></table></div>';
    
                           		document.getElementById('total_type1').value=idss;
                           		return shows_data;
                            }
                        </script>
                           <td align="right"><input type="button" name="Add"  class="addBtn" onClick="javascript:create_type1('add_type1');" alt="Add(+)" > 
                    		<input type="button" name="Add"  class="delBtn"  onClick="javascript:create_type1('delete_type1');" alt="Delete(-)" >
    
        			</td></tr>
    				<tr><td>  </td><td align="left"></td></tr>
                    </table> 
                    
   	                <table width="100%" border="0" class="tbl" bgcolor="#CCCCCC" align="center"><tr><td align="center"></td><td align="center"></td><td align="center"></td></tr>
    
        <tr ><td align="center" width="25%" > </td><td width="10%"> </td><td width="5%"> </td></tr>
    
        <tr>
    
                            <td colspan="7">
    
                            <table cellspacing="3" id="tbl" width="100%">
    
                            <tr>
    
                            <td valign="top" width="8%" align="left">Tax Type</td>
    
                            <td valign="top" width="8%"  align="left">Tax Value(in %)</td>
    
                            
    
                            </tr>
    
                            <tr>
    
                                <td colspan="7">
    
                                <?php
                                /*$select_exc = "select * from inventory_tax_map where inventory_id='".$record_id."' order by inv_tax_map_id asc ";
                                $ptr_fs = mysql_query($select_exc);
                                $s=1;
                                $total_comision= mysql_num_rows($ptr_fs);
                                $total_conditions= mysql_num_rows($ptr_fs);
                                while($data_exclusive = mysql_fetch_array($ptr_fs))
                                { 
                                    $slab_id= $data_exclusive['inv_tax_map_id'];*/
                                ?> 
                                <div class="type1" id="type1_id<?php //echo $s; ?>">
                                <table cellspacing="5" id="tbl<?php //echo $s; ?>" width="100%">
                                    <tr>
                                    <td width="8%" align="center">
                                    <input type="text" name="tax_type<?php //echo $s; ?>" id="tax_type<?php //echo $s; ?>" style=" width:100px" value="<?php //echo $data_exclusive['tax_type'] ?>" />
                                    </td>
                                    <td width="8%" align="center">
                                    <input type="hidden" name="tax_amount<?php //echo $s; ?>" id="tax_amount<?php //echo $s; ?>" value="<?php// echo $data_exclusive['tax_amount'] ?>"/>
                                    <input type="text" name="tax_value<?php //echo $s; ?>" id="tax_value<?php //echo $s; ?>" style=" width:100px" value="<?php //echo $data_exclusive['tax_value'] ?>" onKeyUp="calculte_amount_tax(<?php //echo $s; ?>)"/></td>
                                    <td valign="top" width="10%" align="center">
                                    <?php
                                    /*if($record_id)
                                    {*/
                                    ?>
                                        <input type="hidden" name="total_tax[]" id="total_tax<?php //echo $s; ?>" />
                                        <input type="hidden" name="type1_id<?php //echo $s; ?>" id="type1_id<?php //echo $s; ?>" value="<?php //echo $data_exclusive['inv_tax_map_id'] ?>" />
                                        <input type="button" title="Delete Options(-)" onClick="delete_tax(<?php //echo $s; ?>,'total_type1');" class="delBtn" name="del">
                                        <input type="hidden" name="del_floor_type1<?php //echo $s; ?>" id="del_floor_type1<?php //echo $s; ?>" value="" />
    
                                    <?php 
                                    //} ?>   
                                    </td>
                                    </tr>
                               </table>
                               </div>
                        <?php
                        // $s++;
                        // }
                         ?>
    
                            </tr> 
    
                            </table>
    
                             <input type="hidden" name="total_type1" id="total_type1"  value="0" />
    
                            <div id="create_type1"></div>
    
                        </td></tr></table>
    
                         <?php
                         /*if($record_id)
                         {*/
    
                            ?>
    
    
                        <input type="hidden" name="type1" id="type1" class="inputText" value="<?php //echo $total_conditions; ?>" />
    
                        <?php //} ?> 
    
                        
    
                        </td>
    
                        </tr>
    
                    </table>
    
                 </td>
    
             </tr>-->
    
           <!--============================================================END TABLE 2=========================================-->
             <!---------------------------------------Payment mode-------------------------------------> 
           <tr>
                <td width="14%" class="tr-header">Select Payment Mode <span class="orange_font">*</span></td>
                <td width="74%"><select name="payment_mode" id="payment_mode"  style="width:200px" onChange="payment(this.value)" >
                <option value="">--Select--</option>
                <?php
                $sel_payment_mode="select payment_mode,payment_mode_id from payment_mode";
                $ptr_payment_mode=mysql_query($sel_payment_mode);
                while($data_payment=mysql_fetch_array($ptr_payment_mode))
                {
                    $selected='';
                    if($data_payment['payment_mode_id'] == $row_record['payment_mode_id'])
                    {
                        $selected='selected="selected"';
                    }
                    echo '<option '.$selected.' value="'.$data_payment['payment_mode'].'-'.$data_payment['payment_mode_id'].'">'.$data_payment['payment_mode'].'</option>';
                }
                
                ?>
                </select></td>
             </tr>
             <tr>
             	<td colspan="2">
             	<div id="bank_details" <?php  if($data_payment_mode1['payment_mode']=='Credit Card' || $data_payment_mode1['payment_mode']=='cheque') echo ' style="display:block"'; else echo ' style="display:none"'; ?>>
                <table width="86%">
             		<tr>
             			<td width="19%" class="tr-header" >Bank Name</td>
             			<td width="81%">
						<?php 
                       /* if($_SESSION['type'] !="S")
                        {
                        ?>
                         <select name="bank_name" id="bank_name" onChange="show_acc_no(this.value)">
                         <option value="">--Select--</option>
                         <?php
                         $sle_bank_name="select bank_id,bank_name from bank ".$_SESSION['where_cm_id'].""; 
                         $ptr_bank_name=mysql_query($sle_bank_name);
                         while($data_bank=mysql_fetch_array($ptr_bank_name))
                         {
                            $selected='';
                            if($data_bank['bank_id'] == $row_record['bank_id'])
                            {
                                $selected='selected="selected"';
                            }
                             echo '<option '.$selected.' value="'.$data_bank['bank_id'].'">'.$data_bank['bank_name'].'</option>';
                         }
                         ?>
                         </select>
                          <?php
                         }*/
                         ?>
                         <div id="bank_record">
						<?php 
                       /* if($record_id !='')
                        {
                        ?>
                         <select name="bank_name" id="bank_name" onChange="show_acc_no(this.value)">
                         <option value="">--Select--</option>
                         <?php
                         $sle_bank_name="select bank_id,bank_name from bank where cm_id='".$row_record['cm_id']."'"; 
                         $ptr_bank_name=mysql_query($sle_bank_name);
                         while($data_bank=mysql_fetch_array($ptr_bank_name))
                         {
                            $selected='';
                            if($data_bank['bank_id'] == $row_record['bank_id'])
                            {
                                $selected='selected="selected"';
                            }
                             echo '<option '.$selected.' value="'.$data_bank['bank_id'].'">'.$data_bank['bank_name'].'</option>';
                         }
                         ?>
                         </select>
                          <?php
                         }*/
                         ?></div>
                         <div id="bank_id"></div>
                         </td>
             		</tr>
                    <tr>
                         <td class="tr-header" width="19%">Account No</td>
                         <td><input type="text" name="account_no" readonly="readonly" style="width:200px" id="account_no" value="<?php if($_POST['account_no']) echo $_POST['account_no']; else echo $data_bank_id['account_no']; ?>" /></td>
                    </tr>
                </table>
                </div>
                <div id="bank_ref_no" <?php if($_POST['payment_type'] =='online-5') echo 'style="display:block"'; else if($data_payment_mode1['payment_mode']=='online') echo 'style="display:block"';  else echo 'style="display:none"'; ?>>
                    <table width="100%">
                        <tr>
                            <td class="tr-header" width="16.5%">Bank Ref. no</td>
                            <td ><input type="text" name="bank_ref_no" class="input_text" style="width:200px" id="bank_ref_no" value="<?php if($_POST['bank_ref_no']) echo $_POST['bank_ref_no']; else echo $row_record['bank_ref_no']; ?>"/></td>
                        </tr>
                    </table>
                </div> 
                <div id="chaque_details" <?php  if($data_payment_mode1['payment_mode']=='cheque') echo ' style="display:block"'; else echo ' style="display:none"'; ?>>
                 	<table width="100%">
                   		<tr>
                        	<td class="tr-header" width="19%">Enter Chaque No</td>
                        	<td width="81%"><input type="text" name="chaque_no" id="chaque_no" style="width:200px" value="<?php if($_POST['chaque_no']) echo $_POST['chaque_no']; else echo $row_record['chaque_no']; ?>" /></td>
                        </tr>
                        <tr>
                        	<td class="tr-header" width="19%">Enter Chaque Date</td>
                        	<td><input type="text" name="cheque_date" id="cheque_date" style="width:200px" class="datepicker" value="<?php if($_POST['cheque_date']) echo $_POST['cheque_date']; else echo $row_record['chaque_date']; ?>"  /></td>
                   		</tr>
                    </table>
                 </div>
                 
                 <div id="credit_details" <?php  if($data_payment_mode1['payment_mode']=='Credit Card') echo ' style="display:block"'; else echo ' style="display:none"'; ?>>
                     <table width="100%">
                    
                     <tr>
                     <td class="tr-header" width="19%">Enter Credit Card No</td>
                     <td width="81%"><input type="text" name="credit_card_no" id="credit_card_no" style="width:200px" maxlength="4" value="<?php if($_POST['credit_card_no']) echo $_POST['credit_card_no']; else echo $row_record['credit_card_no']; ?>" /></td>
                     </tr>
                     </table>
                 </div>
                 </td>
               </tr>
               
               <tr>

                <td width="14%" >Final Amount</td>
            
                <td width="74%"><input type="text" name="amount1" id="amount1" style="width:200px" onChange="cal_remaining_amt();" value="<?php if($_POST['save_changes']) echo $_POST['amount1']; else echo $row_record['amount1']; ?>"  /></td>
            
             </tr>
             
             <tr>

                <td width="14%" >Payable Amount</td>
            
                <td width="74%"><input type="text" name="payable_amount" id="payable_amount" style="width:200px" value="<?php if($_POST['save_changes']) echo $_POST['payable_amount']; else echo $row_record['payable_amount']; ?>" onKeyUp="cal_remaining_amt();"/></td>
            
             </tr>
             
             <tr>

                <td width="14%" >Remaining Amount</td>
            
                <td width="74%"><input type="text" name="remaining_amount" id="remaining_amount" style="width:200px" value="<?php if($_POST['save_changes']) echo $_POST['remaining_amount']; else echo $row_record['remaining_amount']; ?>"  /></td>
            
             </tr>
              <!---------------------------------------End Payment mode------------------------------------->
             
            <tr>
                  <td>&nbsp;</td>
                  <td colspan="2"><input type="submit" class="input_btn" value="<?php if($record_id) echo "Update"; else echo "Add";?> Sales Product" name="save_changes"  /> &nbsp;&nbsp;&nbsp;<!--<input type="submit" class="input_btn" value="Save and Print" name="save_changes"  />--></td>
                  
            </tr>
        </table>
        </form>
        <script type="text/javascript">



            $(function() 

            {

                $(".custom_cuorse_submit").click(function(){
                    var cust_name = $("#cust_name").val();
                    var mobile1 = $("#mobile1").val();
                    var email = $("#email").val();
					 var branch_name = $("#branch_name").val();
                  
                    if(cust_name == "" || cust_name == undefined)
                    {
                        alert("Eneter Customer name.");
                        return false;
                    }
                    /*if(mobile1 == "" || mobile1 == undefined)
                    {
                        alert("Enter Mobile no.");
                        return false;
                    }
                    if(email == "" || email == undefined)
                    {
                        alert("Eneter Email ID.");
                        return false;
                    }*/
                    var data1 = 'action=custome_customer_submit&customer_name='+cust_name+'&mobile='+mobile1+'&email='+email+"&branch_name="+branch_name
                    $.ajax({
                        url: "ajax.php", type: "post", data: data1, cache: false,
                        success: function (html)
                        {
							if(html.trim() =='mobile')
							{
								alert("Mobile no. or Email already Exist");
								return false;
							}
							else if(html.trim() =='cust_id')
							{
								alert("Customer Name already Exist");
								return false;
							}
							else if (html.trim() =='blank')
							{
								alert("Please enter Mobile number");
								return false;
							}
							else
							{
								$(".customized_select_box").html(html);
								/*var tax=(service_taxes * course_fee)/100;
								var course_with_tax=Number(course_fee)+Number(tax);
								$("#cust_name").val(course_with_tax);*/
								$('.new_custom_course').dialog( 'close');
								$("#customer_id").chosen({allow_single_deselect:true});
								//getMembership()
							}
                        }
                    });
                });
         });
        </script>
        <div class="new_custom_course" style="display: none;">
            <form method="post" id="jqueryForm" name="discount" enctype="multipart/form-data">
                <table border="0" cellspacing="15" cellpadding="0" width="100%">
                    <tr>
                        <td colspan="3" class="orange_font">* Mandatory Fields</td>
                    </tr>
                    <tr>
                        <td width="20%">Customer Name<span class="orange_font">*</span></td>
                        <td width="40%"><input type="text" class="inputText" name="cust_name" id="cust_name"/></td>
                    </tr>
                    <tr>
                        <td>Mobile<span class="orange_font"></span></td>
                        <td width="40%"><input type="text" class="inputText" name="mobile1" id="mobile1"/></td>
                    </tr>
                    <tr>
                        <td>Email<span class="orange_font"></span></td>
                        <td><input type="text" class="inputText" name="email" id="email"></td>
                    </tr>
                    <tr>
                    
                    <tr>
                        <td></td>
                        <td><input type="button" class="inputButton custom_cuorse_submit" value="Submit" name="submit"/>&nbsp;
                            <input type="reset" class="inputButton" value="Close" onClick="$('.new_custom_course').dialog( 'close');"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>  
        </td></tr>
<?php
                        }   ?>
	 
        </table></td>
    <td class="mid_right"></td>
  </tr>
  <tr>
    <td class="bottom_left"></td>
    <td class="bottom_mid"></td>
    <td class="bottom_right"></td>
  </tr>
</table>

</div>
<!--right end-->

</div>
<!--info end-->
<div class="clearit"></div>
<!--footer start-->
<div id="footer"><? require("include/footer.php");?></div>
<!--footer end-->
<script>
function reset_price(prd_id)
{
	document.getElementById("prod_price"+prd_id).value=0;
	document.getElementById("prod_base_price"+prd_id).value=0;
	document.getElementById("product_disc"+prd_id).value=0;
	<?php 
	if($_SESSION['tax_type']=='GST')
	{
		?>
		document.getElementById("sin_product_cgst"+prd_id).value=0;
		document.getElementById("sin_product_sgst"+prd_id).value=0;
		document.getElementById("sin_product_igst"+prd_id).value=0;
		<?php
	}
	else
	{
		?>
		document.getElementById("sin_product_vat"+prd_id).value=0;
		<?php
	}
	?>
	setTimeout(calc_product_price(prd_id),500);
	//alert(base_price);
	base_price=document.getElementById("base_price"+prd_id).value;
	if(base_price=="1")
	{
		//base_price=0;
		document.getElementById("base_price"+prd_id).value=0;
		document.getElementById("prod_base_price"+prd_id).style.backgroundColor="#cccc";
		document.getElementById("prod_price"+prd_id).style.backgroundColor="white";
		document.getElementById("prod_price"+prd_id).readOnly = false;
		document.getElementById("prod_base_price"+prd_id).readOnly = true;
	}
	else if(base_price=="0")
	{
		//base_price=1;
		document.getElementById("base_price"+prd_id).value=1;
		document.getElementById("prod_price"+prd_id).style.backgroundColor="#cccc";
		document.getElementById("prod_base_price"+prd_id).style.backgroundColor="white";
		document.getElementById("prod_price"+prd_id).readOnly = true;
		document.getElementById("prod_base_price"+prd_id).readOnly = false;
	}
	else
	{
		//base_price=1;
		document.getElementById("base_price"+prd_id).value=1;
	}
}
</script>
<?php
if($_SESSION['type']=="S" || $_SESSION['type']=='Z' || $_SESSION['type']=='LD'  && $record_id=='')
{
	?>
    <script>
	branch_name =document.getElementById("branch_name").value;
	show_bank(branch_name);
	</script>
    <?php
}
/*if($record_id=='')
{
	?>
	<script language="javascript">
	create_floor('add');
	</script>
	<?php
}*/
if($record_id || $_SESSION['type']=="S" || $_SESSION['type']=='Z' || $_SESSION['type']=='LD' )
{
	?>
    <script>
	if(document.getElementById("payment_type"))
	{
		vals= document.getElementById("payment_type").value;
		show_payment(vals);
	}
	var user_type=document.getElementById('user').value;
	if(user_type !='')
	{
		show_data(user_type);
	}
	
	/*if(document.getElementById("branch_name"))
	{
		branch_name =document.getElementById("branch_name").value;
		setTimeout(get_product_list(branch_name),500);
	}*/
	</script>
	<?php
}
else
{
	?>
    <script>
	branch_name=document.getElementById("branch_name").value;
	show_bank(branch_name);
	</script>
	<?php
}
?>
</body>
</html>
<?php $db->close();?>