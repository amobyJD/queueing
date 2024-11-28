<style>
	.left-side{
		position: absolute;
		width: calc(100%);
		height: calc(100%);
		left: 0;
		top:0;
		background: white;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.right-side{
		position: absolute;
		width: 0;
		height: 0;
		right: 0;
		top:0;
		background:gray;
	}
	.slideShow{
		display: flex;
		justify-content: center;
		align-items: center;
		width:0;
		height: 0;
		left: 0;
		right: 0;
		padding: auto;
	}
	.slideShow img,.slideShow video{
		max-width: 0;
		max-height: 0;
		opacity: 0;
		transition: all .5s ease-in-out;
	}
	.slideShow video{
		width: calc(1%);
	}
	a.btn.btn-sm.btn-success {
    z-index: 99999;
    position: fixed;
    left: 1rem;
}
</style>
<?php include "admin/db_connect.php" ?>
<a href="index.php" class="btn btn-sm btn-success"><i class="fa fa-home"></i>Home</a>
<div class="left-side">
	<div class="col-md-10 offset-md-1">
		<div class="card">
			<div class="card-body">
				<div class="container-fluid">
					<form action="" id="new_queue">
						
						<div class="form-group">
						<label for="name" class="control-label">Name</label>
						<td><input type="text" class="form-control" name="vname" id="name" placeholder="" required></td>
						<label for="position" class="control-label">Position</label>
						<td><input type="text" class="form-control" name="position" id="position" placeholder="" required></td>
						<label for="purpose" class="control-label">Purpose</label>
						<td><input type="text" class="form-control" name="purpose" id="purpose" placeholder="" required></td>
						<label for="purpose" class="control-label">Office/School</label>
						<td><input type="text" class="form-control" name="office_school" id="office_school" placeholder="" required></td>
							<label for="transaction_id" class="control-label">Select Transaction</label>
							<select name="transaction_id" id="transaction_id" class="custom-select browser-default select2" required>
									<option></option>
									<?php 
										$trans = $conn->query("SELECT *, t.id as tid FROM transactions as t inner join transaction_windows as tw on tw.transaction_id = t.id where tw.status = 1 order by tw.section_name asc;");
										while($row=$trans->fetch_assoc()):
									?>
									<option value="<?php echo $row['tid'] ?>"><?php echo $row['name']," - ",$row['section_name'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-md-3 float-right ">Submit</button>
							</div>
						</div>
					</form>
				</div>
				
				<!-- Modal -->
				<div class="modal fade" id="queue-details-modal" tabindex="-1" role="dialog" aria-labelledby="queue-details-modal-label" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="queue-details-modal-label">Queue Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
						<p>Name: <span id="vname"></span></p>
						<p>Position: <span id="position"></span></p>
						<p>Purpose: <span id="purpose"></span></p>
						<p>Queue No: <span id="queue_no"></span></p>
					  </div>
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					  </div>
					</div>
				  </div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<div class="right-side">
	<?php
	$uploads = $conn->query("SELECT * FROM file_uploads order by rand() ");
	$slides = array();
	while($row= $uploads->fetch_assoc()){
		$slides[] = $row['file_path'];
	}
	?>
	<div class="slideShow">
		
	</div>
</div>
<script>
	var old_wait_no ="";
	var slides = <?php echo json_encode($slides) ?>;
	var scount = slides.length;
		if(scount > 0){
				$(document).ready(function(){
					render_slides(0)
				})
		}
	function render_slides(k){
		if(k >= scount)
			k = 0;
		var src = slides[k]
		k++;
		var t = 0
		var file ;
			t = t[1];
			if(t == 'mp4'){
				file = $("<video id='slide' src='admin/assets/uploads/"+src+"' onended='render_slides("+k+")' autoplay='true' muted='muted'></video>");
			}else{
				file = $("<img id='slide' src='admin/assets/uploads/"+src+"' onload='slideInterval("+k+")' />" );
			}
			console.log(file)
			if($('#slide').length > 0){
				$('#slide').css({"opacity":0});
				setTimeout(function(){
						$('.slideShow').html('');
				$('.slideShow').append(file)
				$('#slide').css({"opacity":1});
				if(t == 'mp4')
					$('video').trigger('play');

				
				},500)
			}else{
				$('.slideShow').append(file)
				$('#slide').css({"opacity":1});

							}
				
	}
	function slideInterval(i=0){
		setTimeout(function(){
		render_slides(i)
		},500)

	}
	$('.select2').select2({
		placeholder:"Please Select Here",
		width:"100%"
	})
	$('#new_queue').submit(function(e){
    e.preventDefault();
    
    $.ajax({
        url:'admin/ajax.php?action=save_queue',
        method:'POST',
        data:$(this).serialize(),
        beforeSend: function() {
            // start_load();
        },
        error:function(err){
            console.log(err);
            alert_toast("An error occurred", 'danger');
            alert_toast("Queue Registered Successfully", 'success');
            end_load();
        },
        success:function(resp){
            if(resp > 0){
                $('#vname').val('');
                $('#position').val('');
                $('#name').val('');
                $('#purpose').val('');
				$('#office_school').val('');
                $('#transaction_id').val('').select2({
                    placeholder: "Please Select Here",
                    width: "100%"
                });
                $.ajax({
                    url: 'admin/ajax.php?action=get_queueid&id=' + resp,
                    method: 'GET',
                    success: function(resp){
                        resp = JSON.parse(resp);
                        let wait_list = resp.data;
                        $('#queue-details-modal #vname').text(resp.vname);
                        $('#queue-details-modal #position').text(resp.position);
                        $('#queue-details-modal #purpose').text(resp.purpose);
                        $('#queue-details-modal #queue_no').text(resp.queue_no);
                        $('#queue-details-modal').modal('show');
                        $('button.print').off('click').on('click', function() {
                            printModal();
                        });
                    }
                });
                alert_toast("Queue Registered Successfully", 'success');
                playSoundwait(wait_list[wait_list.length - 1]);
            }
        }
    })
})

function playSoundwait(waiting) {
	if (waiting.queue_no != old_wait_no){
		var textToSpeak = 'Welcome to the Division Office, '+waiting.vname.toLowerCase()+'.'+' '+'Were glad to have you here. Please take a seat and relax while you wait for your queue number to be called. Our team will assist you shortly. Thank you for your patience!';
        responsiveVoice.speak(textToSpeak, "US English Male", {rate: .8});
        // Using SpeechSynthesis API for audio announcement
		// var synth = window.speechSynthesis;
		// var utterance = new SpeechSynthesisUtterance(textToSpeak);
		// synth.speak(utterance);
		old_wait_no = waiting.queue_no;
	};
}
function printModal() {
    let vname = $('#queue-details-modal #vname').text();
    let position = $('#queue-details-modal #position').text();
    let queue_no = $('#queue-details-modal #queue_no').text();
	let purpose = $('#queue-details-modal #purpose').text();

    // Create a new window
    let printWindow = window.open('', '', 'height=240,width=336');

   
    printWindow.document.write('<html><head><title>DepEd LDN</title>');
    printWindow.document.write('<style>@page { size: auto; margin: 0mm; } body { margin: 0; }</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>Queue Details</h1>');
    printWindow.document.write('<p>Name: ' + vname + '</p>');
    printWindow.document.write('<p>Position: ' + position + '</p>');
	printWindow.document.write('<p>Purpose: ' + purpose + '</p>');
    printWindow.document.write('<p>Queue No: ' + queue_no + '</p>');
    printWindow.document.write('</body></html>');


    printWindow.document.close();


    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}

</script>