<?php include 'db_connect.php' ?>
<style>
   
</style>

<div class="containe-fluid">

	<div class="row">
		<div class="col-lg-12">
			
		</div>
	</div>

	<div class="row mt-3 ml-3 mr-3">
			<div class="col-lg-12">
    			<div class="card">
    				<div class="card-body">
    				<?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
    									
    				</div>
    				<hr>
    				
    		      </div>
                </div>
	</div>
<hr>
<?php if($_SESSION['login_type'] == 2): ?>
<?php 

?>
<script src="https://code.responsivevoice.org/responsivevoice.js?key=Ag3OjLP3"></script>
<script>
    var home_queue_no = "";
    function queueNow(row_id){
    var data = {
        row_id: row_id
    };
    $.ajax({
        url:'ajax.php?action=update_queue',
        method:'POST',
        data:data,
        dataType:'json',
        success:function(response){
         if(response > 0){
					$.ajax({
					url:'ajax.php?action=get_queue_user',
					method:"POST",
					data:{id:'<?php echo $_SESSION['login_id'] ?>'},
					success:function(resp){
						resp = JSON.parse(resp)
						if(resp.status == 0){
							$('#serving-list').empty();
							$.each(resp.data, function(index, value){
								$('#serving-list').append('<tr><td>'+value.vname+'</td><td>'+value.position+'</td><td>'+value.purpose+'</td><td>'+value.queue_no+'</td><td>'+value.wname+'</td><td><button class="btn btn-primary" onclick="queueDone(' + value.id +')">Done</button></td></tr>');
							});
						}
					}
				})
					alert_toast("Customer Accepted Successfully",'success');
				}
        }
    });
}

function queueDone(row_id) {
    var data = {
        row_id: row_id
    };
    $.ajax({
        url: 'ajax.php?action=done_queue',
        method: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response > 0) {
                // Remove the row associated with the clicked "Done" button
                $(this).closest('tr').remove();
                
                // Get the updated serving list data
                $.ajax({
                    url: 'ajax.php?action=get_serve_user',
                    method: 'POST',
                    data: {
                        id: '<?php echo $_SESSION['login_id'] ?>'
                    },
                    success: function(resp) {
                        resp = JSON.parse(resp);
                        if (resp.status == 1) {
                            $('#serving-list').empty();
                        }
                    },
					complete:function(){
							window.open("http://www1.depedldn.com/que/admin/index.php?page=home","_self");
					}
                })
                alert_toast("Action Completed Successfully", 'success');
            }
        }.bind(this) // bind the context of the clicked button to the AJAX success function
    });
}

	$(document).ready(function(){
        var renderServe = setInterval(function(){
            $.ajax({
                url: 'ajax.php?action=get_queue_user',
                method: "POST",
                data: {id: '<?php echo $_SESSION['login_id'] ?>'},
                success: function(resp){
                    try {
                        var data = JSON.parse(resp);
                        if (data.status === 0) {
                            $('#waiting-list').empty();
							let waiting_list = data.data;
                            $.each(data.data, function(index, value){
                                $('#waiting-list').append('<tr><td>'+value.vname+'</td><td>'+value.position+'</td><td>'+value.purpose+'</td><td>'+value.queue_no+'</td><td>'+value.wname+'</td><td><button class="btn btn-primary" onclick="queueNow(' + value.id +')">Accept</button></td></tr>');
							playSound(waiting_list[waiting_list.length-1]);
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching queue user:', status, error);
                }
            });

        }, 1500);

    });
	
	$(document).ready(function(){
    var renderServe = setInterval(function(){
        $.ajax({
            url:'ajax.php?action=get_serve_user',
            method:"POST",
            data:{id:'<?php echo $_SESSION['login_id'] ?>'},
            success:function(resp){
                resp = JSON.parse(resp)
                if(resp.status == 1){
                    $('#serving-list').empty();
					let serving_list = resp.data;
                    $.each(resp.data, function(index, value){
                        $('#serving-list').append('<tr><td>'+value.vname+'</td><td>'+value.position+'</td><td>'+value.purpose+'</td><td>'+value.queue_no+'</td><td>'+value.wname+'</td><td><button class="btn btn-primary" onclick="queueDone(' + value.id +')">Done</button></td></tr>');
                    });
                }
            }
        })
    },1500)
});
function playSound(waiting) {
	if (waiting.queue_no != home_queue_no){
		var textToSpeak = waiting.queue_no+' '+waiting.vname+' is waiting';
        responsiveVoice.speak(textToSpeak, "US English Female", {rate: .7});
    // Using SpeechSynthesis API for audio announcement
		//var synth = window.speechSynthesis;
		//var utterance = new SpeechSynthesisUtterance(textToSpeak);
		//synth.speak(utterance);
		home_queue_no = waiting.queue_no;
	};
}

</script>

</script>
<div class="left-side">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header bg-primary text-white"><h3 class="text-center"><b>Waiting Customer</b></h3></div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Purpose</th>
                                        <th>Queue No.</th>
                                        <th>Window</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="waiting-list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="left-side">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header bg-primary text-white"><h3 class="text-center"><b>Now Serving</b></h3></div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Purpose</th>
                                        <th>Queue No.</th>
                                        <th>Window</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="serving-list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>


</div>
<script>
	
</script>