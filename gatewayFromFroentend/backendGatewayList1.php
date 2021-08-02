  <!DOCTYPE html>
  <html>
  <head>
    <style type="text/css">
      .percentSign {
        padding: 0 10px 0 9px;
      }
      .createGatewayModalBody .row {
        padding: 5px 0;
      }
    </style>


    <script>


      function reloadxml(){

        $.ajax({
          type:"POST",
          url : base_url+"/index.php/json/reloadxml",
          dataType : 'json',
          data :  {'reloadxmlajax':true},
          beforeSend: function () {
            $(".loader").show();
          },
          success : function(data,status,request){
           $(".loader").hide();
           console.log(data);
           console.log(status);
           console.log(request);
           if (data.status == "xmlcmdsuccessful"){
            alertify.success("XML is OK");
          }else if (data.status == "xmlcmdunsuccessful"){
            alertify.error("XML not OK, kindy check status of media server ");
          }else{
            alertify.error("something else, kindly check xml file");
          }
        },

        error : function(xhr,status,error,responce){
         $(".loader").hide();
         console.log("request is failed");
         console.log("xhr : ",xhr);
         console.log("status : ",status);
         console.log("error : ",error);
         console.log("responce : ",responce);
       }
     })
      }

      function reloadsofia(){
        $.ajax({
          type : "POST",
          url : base_url+"/index.php/json/reloadmodsofia",
          dataType : 'json',
          data :  {'reloadsofiarequest':true},
          beforeSend: function () {
            $(".loader").show();
          },
          success : function(data,status,responce){
            $(".loader").hide();
            console.log("data.status ::: ",data.status);
            console.log("status ::: ",status);
            console.log("responce ::: ",responce);
            $x = data.status;
            console.log("x ::: ",$x);
            switch($x){
              case "sofiacmdsuccessful" : alertify.success("gateway list successfully updated in media server");
              break;
              case "sofiacmdunsuccessful" : alertify.error("Error ! kindly check if media server is running");
              break;
              case "callsAreMoreThanZero" : alertify.error("Error ! calls are running ");
              break;
              case "showCallsCountError" : alertify.error("Error ! kindly check if media server is running");
              break;
              case "noVariableRequestError" : alertify.error("Error ! Request Error");
              break;
            }
          },
          error : function(xhr,status,error,responce){
            $(".loader").hide();
            console.log("xhr : ",xhr);
            console.log("status : ",status);
            console.log("error : ",error);
            console.log("responce : ",responce);
          }

        })

      }


      function removeGateway(nameReceived,valueReceived){
  //alert(nameReceived+" : "+valueReceived);
  //alert(valueReceived);
  $.ajax({
   type : "POST",
   url : base_url+"/index.php/json/removeFreeswitchXmlGateway",
   dataType : 'json',
   data :  {'removeGateway':true,nameReceived:valueReceived},
   success : function(data,status,responce){   
     $x = data.status;
     console.log("x ::: ",$x);
     console.log("data.status ::: ",data.status);
     console.log("status ::: ",status);
     console.log("responce ::: ",responce);
     switch($x){
      case "GatewayDeleted" : alertify.success("Success ! Gateway Deleted");
      break;
      case "GatewayNameNotMatchedDeleted" : alertify.error("Error ! Gateway Name Not Matched please refresh once");
      break;
      case "unknown" : alertify.error("Error ! kiindly check if xml file exist ");
      break;
      
    }
    if ($x == "GatewayDeleted" ){
      window.location.reload();
    }

  },

  error : function(xhr,status,error,responce){
    console.log("xhr : ",xhr);
    console.log("status : ",status);
    console.log("error : ",error);
    console.log("responce : ",responce);
  }

})

}


</script>

</head>
<body>
  <div class="row">
    <div class="col-lg-12" style="background: #fff;">
      <header class="panel-heading">

       Sip Gateway Config

       <span class="pull-right" >&nbsp</span>

       <button type="button" onClick="window.location.reload();"  class="btn btn-primary btn-xs pull-right createGateway" type="button"   style="padding: 4px 8px;"  >  <i class="fa fa-refresh"></i></button>

       <span class="pull-right" >&nbsp</span>

       <button type="button" onclick="reloadsofia();" id="reloadxmlbtn" class="btn btn-primary btn-xs pull-right createGateway" type="button"   style="padding: 4px 8px;"  > Reload Gateway List</button>

       <span class="pull-right" >&nbsp</span>


       <button type="button" onclick="reloadxml();" id="reloadxmlbtn" class="btn btn-primary btn-xs pull-right createGateway" type="button"   style="padding: 4px 8px;"  > Reload Xml Data</button>



       <span class="pull-right" >&nbsp</span>


       <a href="<?php echo base_url(); ?>index.php/site/addBackendGateway" class="btn btn-primary btn-xs pull-right createGateway" type="button"
        style="padding: 4px 8px;">
        <i class="fa fa-plus"></i> Create
      </a>


    </header>
    <table id="gatewayDetailTbl" class="table table-striped table-hover" style="width = 100%">
      <thead>
        <tr>
          <th>Name</th>
          <th>Ip</th>
          <th>registration</th>
          <th>status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);  

        $server_ip = $_SERVER['SERVER_ADDR'];
        function getRegistrationStatus($nameOfGateway){
          $server_ip = $_SERVER['SERVER_ADDR'];
        //echo $nameOfGateway;
          exec("/usr/local/bin/fs_cli -H $server_ip  -P 8025 -p VoiTekk4321  -x 'sofia status gateway "  .$nameOfGateway. "'", $output, $return);
          if ($return == 0){
            $a  = $output;
            $b = $a[19];
            $c = explode(' ', $b);
            $d = $c[3];
            $e = trim($d);
            switch($e){
              case "NOREG" : $e = "NOT_REGISTRED";break;
              case "REGED" : $e = "REGISTRED";break;
              default : $e = "TRYING";
            }
            print_r($e);

          }
        }

        $dom = new DOMDocument();
        $dom->load('sip/gateways.xml') or die("unable to open gateway.xml<br>");

        $gatewayNameArray = $dom->getElementsByTagName('gateway');
//$gatewayNameValue = $gatewayNameArray[0]->getAttribute('name');

//print_r($gatewayNameValue);

        foreach ($gatewayNameArray as $values) {
    # echo '<table>';
          echo '<tr role="row" class="odd" >';
          echo "<td  style='color: #464749;font-size: 100%;font-weight: normal;' >";

    //echo $values;
          $x = $values->getAttribute('name');
          echo $x;
          echo '</td>';

          echo "<td  style='color: #464749;font-size: 100%;font-weight: normal;' >";
          $paramValIp = $values->getElementsByTagName('param')->item(0);
          $paramListIp = $paramValIp->getAttribute('value');
          echo $paramListIp;
          echo '</td>';

          echo "<td  style='color: #464749;font-size: 100%;font-weight: normal;' >";
          $paramValReg = $values->getElementsByTagName('param')->item(1);
          $paramListReg = $paramValReg->getAttribute('value');
          echo $paramListReg;
          echo '</td>';

          echo "<td  style='color: #464749;font-size: 100%;font-weight: normal;' >";
          getRegistrationStatus($x);
          echo '</td>';

          echo "<td> ";
          echo '<button type="button" name="removeGateway" value="' . $values->getAttribute('name') . '" class="btn btn-danger btn-xs deleteGateway" onclick="removeGateway(this.name,this.value);" style="padding: 5px;"> <i class="fa fa-trash"></i></button>';
          echo "&nbsp";
          echo '<a style="padding:5px 7px;" 
                   class="btn btn-success btn-xs campaignTooltip"   
                    href="editBackendGatewayList?pageName=editBackendGatewayList&gatewayName='. $values->getAttribute('name') .'&gatewayIp='.$paramListIp.'&gatewayReg='.$paramListReg.'"
                >
                    <i class="fa fa-pencil"></i></a>';
          echo '</td>';




          echo '</tr>';

#  echo '</table>';
        }
        $dom->save("sip/gateways.xml");
        ?>

      </tbody>
    </table>
  </div>
</div>
</body>
</html>
