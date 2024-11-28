<!DOCTYPE html>
<html>
<head>
<style>
body {
  width: 100%;
  height: 100%;

}
th.card-header {
  background-color: LightGray;
}

.left-side {
  width: 79%;
  height: 70%;
  float: left;
  position: fixed;
  left: 0;
  top: 0;
}

.right-side {
  width: 20%;
  height: 100%;
  background-color: black;
  float: right;
  position: fixed;
  right: 0;
  top: 0;
  overflow: hidden;
}
/* CSS styles for the moving text */
.textContainer {
		position: relative;
		overflow: hidden;
		height: 100vh; /* Set the container height to viewport height */
}

.movingText {
	position: absolute;
	left: 50%;
	transform: translateX(-50%);
	font-size: 24px;
	background-color: #f1f1f1;
	padding: 10px;
	border-radius: 5px;
	box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
	opacity: 0; /* Hide text initially */
}
@keyframes verticalMove {
    0%, 10% {
        transform: translateY(100vh); /* Slightly above the top to create a softer appearance */
    }
    100% {
        transform: translateY(-210px);
    }
}
.vertical-text {
    font-size: 30px;
    animation: verticalMove 30s cubic-bezier(0.48, 0.05, 0.01, 1.50) infinite; /* Use cubic-bezier for a smoother transition */
}

.footer {
  width: 79%;
  height: 29%;
  background-color: Iron oxide red;
  position: fixed;
  bottom: 0;
  left: 0;
}
.footer1 {
  width: 20%;
  height: 10%;
  background-color: black;
  position: fixed;
  bottom: 0;
  right: 0;
}
</style>
<?php include "admin/db_connect.php" ?>
<?php 
$tname = $conn->query("SELECT * FROM transactions where id =".$_GET['id'])->fetch_array()['name'];
function nserving(){
	include "admin/db_connect.php";

	$query = $conn->query("SELECT q.*,t.name as wname FROM queue_list q inner join transaction_windows t on t.id = q.window_id where date(q.date_created) = '".date('Y-m-d')."' and q.status = 1 order by q.id desc ");
	if($query->num_rows > 0){
			$data = array(); //initialize an empty array
			while ($row = $query->fetch_assoc()) {
				$data[] = $row; //add fetched row to data array
			}
			return json_encode(array('status'=>1,"data"=>$data));
		}else{
			return json_encode(array('status'=>0));
		}
	$conn->close();
}
?>

<a href="index.php" class="btn btn-sm btn-success"><i class="fa fa-home"></i></a>
</head>
<body>
<div class="left-side">
<div class="card h-100">
	<div class="card-header bg-primary text-white"><h3 class="text-center"><b>Now Serving</b></h3></div>
		<table class="table">
			
			<thead>
				<tr>
					<th class="card-header"><h3 class="text-left"><b>Queue</b></h3></th>
                    <th class="card-header"><h3 class="text-left"><b>Client Name</h3></b></th>
					<th class="card-header"><h3 class="text-left"><b>Office</h3></b></th>
				</tr>
			</thead>
			
			<h3>
			<tbody id="serving-list">
			</tbody>
			</h3>
		</table>
</div>
</div>
<div class="right-side">
    <div class="container verticalContainer vh-100">
        
    </div>
</div>
<div class="footer">
	<div class="card h-100">
		<div class="card-header bg-primary text-white"><h5 class="text-center"><b>Waiting</b></h5></div>
		<div class="card-body">
			<!--<table class="table">
				<thead>
					<tr>
						
					</tr>
				</thead>
				<tbody id="wait-list">
				</tbody>
			</table>-->
			<div class="d-flex flex-nowrap" id="wait-list" style="width:100%">
			</div>
		</div>
	</div>
</div>

</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.responsivevoice.org/responsivevoice.js?key=Ag3OjLP3"></script>
<script>
    var old_wait_no ="";
	var old_queue_no = "";
    function vertical_display(){
        $.ajax({
            url:"admin/php_functions.php",
            type:"POST",
            data:{activity:true},
            success:function(returnedData){
             
                var data = JSON.parse(returnedData);
                var textContainer = document.querySelector('.verticalContainer');

                function displayItem(index) {
                    var date_to = new Date(data[index].when_at);
                    var date_from = new Date(data[index].when_to);
                    var formattedDate_to = date_to.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    var formattedDate_from = date_from.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    var textElement = document.createElement('div');
                    textElement.className = 'card px-2 vertical-text';
                    textElement.innerHTML = "<span style='color: Blue;'>What:</span>" + data[index].what + "<br><span style='color: Blue;'>From:</span> " + formattedDate_from + "<br><span style='color: Blue;'>To:</span> " + formattedDate_to + "<br><span style='color: Blue;'>Where:</span> " + data[index].where_at + "<br><span style='color: Blue;'>Who:</span> " + data[index].who;
                    textContainer.innerHTML = ''; 
                    textContainer.appendChild(textElement);

                    setTimeout(function () {
                        if (index + 1 < data.length) {
                            displayItem(index + 1); 
                        } else {
                            vertical_display();
                        }
                    }, 20000); // 6000 milliseconds (6 seconds)
                }
                displayItem(0);
            },
            error:function(err){
                console.log(err)
            }   
        })
    }
	
	$(document).ready(function(){
        vertical_display();
        var renderServe = setInterval(function(){
            $.ajax({
                url:'admin/ajax.php?action=get_queue',
                method:"POST",
                data:{id:'<?php echo $_GET['id'] ?>'},
                success:function(resp){
                    resp = JSON.parse(resp)
                    if(resp.status == 1){
                        $('#serving-list').empty();
						let serving_list = resp.data;
                        $.each(resp.data, function(index, value){
                            $('#serving-list').append('<tr class="px-1"><div class="card"><div class="card-header"><td><h4><b>'+value.queue_no+'</b></h4></td><td><h4>'+value.vname+'</h4></td><td><h4>'+value.wname+'</h4></td></div></div></tr>');
						playSound(serving_list[serving_list.length-1]);
                        });
                    }
                }
            })
        },1200)
        var renderServe1 = setInterval(function(){
        $.ajax({
            url:'admin/ajax.php?action=get_wait',
            method:"POST",
            data:{id:'<?php echo $_GET['id'] ?>'},
            success:function(resp){
                resp = JSON.parse(resp)
                if(resp.status == 0){
                    $('#wait-list').empty();
					let wait_list = resp.data;
                    $.each(resp.data, function(index, value){
						//count++;
						//if(count <= 5){
							$('#wait-list').append('<p class="px-1"><div class="card"><div class="card-header"><h3><b>'+value.queue_no+'</b></h3></div><div class="card-body"><h3><b><td>'+value.wname+'</b></h3></div></div></p>');
						//}
                       // playSoundwait(wait_list[wait_list.length-1]);
                    });
                    
                }
            }
        },1200)
    })
})
function playSound(serving) {
	if (serving.queue_no !== old_queue_no){
		var textToSpeak = 'Now serving,'+' '+serving.queue_no+'.'+' '+serving.vname.toLowerCase()+' to '+serving.wname;
        responsiveVoice.speak(textToSpeak, "US English Male", {rate: .9});
        // Using SpeechSynthesis API for audio announcement
		// var synth = window.speechSynthesis;
		// var utterance = new SpeechSynthesisUtterance(textToSpeak);
		// synth.speak(utterance);
		old_queue_no = serving.queue_no;
	};
}
function playSoundwait(waiting) {
	if (waiting.queue_no != old_wait_no){
		var textToSpeak = 'Welcome to the Division Office, '+waiting.vname.toLowerCase()+'.'+' '+'Were glad to have you here. Please take a seat and relax while you wait for your queue number to be called. Our team will assist you shortly. Thank you for your patience!';
        responsiveVoice.speak(textToSpeak, "US English Male", {rate: .9});
        // Using SpeechSynthesis API for audio announcement
		// var synth = window.speechSynthesis;
		// var utterance = new SpeechSynthesisUtterance(textToSpeak);
		// synth.speak(utterance);
		old_wait_no = waiting.queue_no;
	};
}


</script>
</html>
