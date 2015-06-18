
    <!-- Stripped Down Version of Index.php that Provides Graphing Functions in the same website format -->
<?
    include 'functions.php';
	include("header.html");

?>


<!-- Food Co-oP Website Header Copied from index.php -->
<!DOCTYPE html>
<html lang="en">

  <!-- Main Container for Graph Objects -->
  <div class="container">
  
       <div class="theme-showcase">
           <div class='page-header'>
               <h1>The Food CO-OP Stock </h1>	
               
           </div>
        	<div class="well">
			<p>For Bar Graph, input form [query1],[query2],....</p>
			</div>
        
        
        <!-- Form for Bar Graph Input Variables, of the form x,y,z.....,a  for each search element -->
		
	   	<?php
			if($_REQUEST["page"]=="bar"){
			echo '<form action="Graphs.php?page=bar" method="post">
			Search Terms: <input type="text" name="dN"><br>
			<input type="checkbox" name="Sum" value=false>Sum of Product Stock<br>
			<input type="checkbox" name="Sum" value=true>Amount of Products <br>
			<input type="submit" class="btn btn-sm btn-default" >
		</form>';
			}else if($_REQUEST["page"]=="pie"){
			echo '<form action="Graphs.php?page=pie" method="post">
			Search Terms: <input type="text" name="dN">
			<input type="submit" class="btn btn-sm btn-default" >
		</form>';
			}
		
		
		?>
		

		<!-- Graph Object -->
        <div id="graph" style="height: 500px;"></div>
		

       </div>
       </div>

	   
	<!-- Script Additions -->
	<script src="../../assets/js/jquery.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <script src="../../assets/js/holder.js"></script>
    <script src="../../assets/js/jquery.validate.js"></script>
    <script src="../../assets/js/raphael-min.js"></script>
    <script src="../../assets/js/morris.min.js"></script>
	
	<!-- Main Script for Graphing Capabilities -->
    <script type="text/javascript">
	
	var myV  = <?php echo json_encode($_REQUEST["page"]); ?>;
	//First Morris Graph for Donut Graph -> 
	if(myV == "gst"){
		new Morris.Donut({

			element: 'graph',

			data: <? printGSTvsFRE(); ?>,
		});


	}else if((myV == "bar")){
		//Second Graph for Search bar Graph
		new Morris.Bar({
			
			element: 'graph',
			//Data Element that uses request to figure out what graph to render

			data:  <? 
					if($_REQUEST["page"]=="bar"&&$_REQUEST["dN"]!=""){
							find($_REQUEST["dN"],$_REQUEST["Sum"]); 
						}else{echo "'null'";}?>,
	
			xkey: 'Product',
			
			ykeys: ['value'],
			
			labels: ['Amount'],
		});
	
	}else if((myV == "pie")){
			new Morris.Donut({

			element: 'graph',

			data: <? printPie($_REQUEST["dN"]); ?>,
		});
	
	}
	

	
    </script>
	

  </body>
</html>
