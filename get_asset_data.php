function get_asset_data() {
  // create an array to store the data
  var data = [];
  
  // retrieve data from the database
  <?php
    $sql = "SELECT asset_id, MONTH(date) AS month, COUNT(*) AS count FROM asset GROUP BY asset_id, month";
    $result = mysqli_query($conn, $sql);
    
    // iterate through the result set and store the data in the array
    while ($row = mysqli_fetch_assoc($result)) {
      // find the index of the asset_id in the data array
      var index = data.findIndex(function(d) { return d.label === "Asset_ID " + row.asset_id; });
      
      // if the asset_id is not yet in the data array, add it
      if (index < 0) {
        data.push({
          label: "Asset_ID " + row.asset_id,
          backgroundColor: 'rgba(60,141,188,0.9)',
          borderColor: 'rgba(60,141,188,0.8)',
          pointRadius: false,
          pointColor: '#3b8bba',
          pointStrokeColor: 'rgba(60,141,188,1)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data: []
        });
        
        index = data.length - 1;
      }
      
      // fill in the data for the month
      var count = row.count;
      var monthIndex = parseInt(row.month) - 1;
      
      data[index].data[monthIndex] = count;
    }
    
    // pad the data with 0s for missing months
    for (var i = 0; i < data.length; i++) {
      for (var j = 0; j < 12; j++) {
        if (typeof data[i].data[j] === 'undefined') {
          data[i].data[j] = 0;
        }
      }
    }
    
    // convert the data to the format used by the chart
    var chartData = {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      datasets: data
    };
  ?>
  
  // return the chart data
  return chartData;
}
