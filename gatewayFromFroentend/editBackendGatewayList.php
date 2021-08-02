          <?php 
          $pageNameReceived = $_GET['pageName'];
          $gatewayNameReceived = $_GET['gatewayName'];
          $gatewayIpReceived = $_GET['gatewayIp'];
          $gatewayRegReceived = $_GET['gatewayReg'];
          
          ?>
          <!DOCTYPE html>
          <html>
          <head></head>
          <body>

            <div class="row">
             <div class="col-lg-8" style="background: #fff;">
               <header class="panel-heading">
                Edit Gateway Details
                <span class="pull-right" >&nbsp</span>
                <button type="button"   class="btn btn-primary btn-xs pull-right " type="button"   style="padding: 4px 8px;"  >  Back
                </button>
                <span class="pull-right" >&nbsp</span>
              </header>


              <div class="row">
                <div class="col-md-12 ">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="box paint color_12">
                        <div class="content">


                          <form class="form-horizontal row" method="post" action="<?php echo site_url('site/updateBackendGateway'); ?>">

                            <div class="form-row form-group row">
                              <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">Gateway Name</label>
                              <div class="controls col-md-9">
                                <input type="text" id="normal-field" class="form-control" name="gatewayName" value="<?php echo $gatewayNameReceived; ?>"  placeholder="gateway name" required>
                              </div>
                            </div>

                            <div class="form-row form-group row">
                              <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">Gateway Ip / proxy value</label>
                              <div class="controls col-md-9">
                                <input type="text" id="normal-field" class="form-control" name="gatewayIp" value= "<?php echo $gatewayIpReceived; ?>"  placeholder="gateway Ip" required>
                              </div>
                            </div>


                            <div class="form-row form-group row">
                              <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">Gateway Registration</label>
                              <div class="controls col-md-9">
                                <select name="gwRegister" id="gwRegister" id="normal-field" class="form-control" onchange="setRemember(this.value)">
                                  <?php
                                  if($gatewayRegReceived == "false"){
                                    echo "<option value='true' >True</option>";
                                    echo "<option value='false' selected>False</option>";
                                  }else{
                                    echo "<option value='true' selected>True</option>";
                                    echo "<option value='false' >False</option>";
                                  }
                                  ?>
                                </select>

                              </div>
                            </div>


                            <div class="form-row form-group row" id="rememberMsg" style="display: none">
                             <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">&nbsp</label>
                             <div class="controls col-md-9" style="color:#0275d8">
                               Remember ! to set <span style="font-weight: bold;">username</span> and <span style="font-weight: bold;">password</span> if registration is true
                             </div>
                           </div>

                           <div class="form-row form-group row">
                             <label class="control-label col-md-3" style="padding-left: 35px;">Add Fields</label>
                             <div class="controls col-md-9">
                              <table id="additionalParam"  class="table table-striped table-hover" style="width = 100%">
                                <thead>
                                  <tr>

                                    <td>parameter</td>

                                    <td>Value</td>

                                    <td><button type="button" id="addPara" name="addPara" onclick="addRow()" class="btn btn-sm btn-info">
                                      <span class="glyphicon glyphicon-plus-sign addjson  pull-right"></span>
                                    </button>
                                  </td>
                                </tr>
                              </thead>
                              <tbody>
                               <?php
                               ini_set('display_errors', 1);
                               ini_set('display_startup_errors', 1);
                               error_reporting(E_ALL);

                               $dom = new DOMDocument();
                               $dom->load('sip/gateways.xml') or die("unable to open gateway.xml<br>");
                               $xpath = new DOMXPath($dom);
                               // $paramName = $_GET['pageName'];
                               // $paramValue = $_GET['gatewayName'];
                               $gatewayNameArray = $dom->getElementsByTagName('gateway');

                               foreach ($gatewayNameArray as $key => $value) {
                                $gatewayName = $value->getAttribute('name');
                                if ($gatewayName == $gatewayNameReceived) {
                                  $query = "/profile/gateways/gateway[@name='$gatewayNameReceived']/child::*";
                                  $node = $xpath->query($query);
                                  $nodeLength = $node->length;
        // echo ":::nodeLength:::$nodeLength:::";

                                  foreach ($node as $key => $value1) {
                                    $e = $value1->getAttribute('name');
                                    $f = $value1->getAttribute('value');
                                    if ($e == "proxy" || $e == "register"   ){
                                      continue;
                                    } 
                                    echo "<tr role='row' class='odd'>";
                                    echo "<td style='color: #464749;font-size: 100%;font-weight: normal;'>";
                                    echo "<input type='text' name='nameOfParamText1[]' value='$e'>";
                                    echo "</td>";
                                    echo "<td style='color: #464749;font-size: 100%;font-weight: normal;'>";
                                    echo "<input type='text' name='nameOfValText1[]' value='$f'>";
                                    echo "</td>";

                                    echo "<td style='color: #464749;font-size: 100%;font-weight: normal;'>";

                                    echo "<input type='button'   name='parameter" . ($key + 1) . "'  value='-' onclick='removeRow(this.name)' class='btn btn-sm btn-info'>";
                                    echo "</td>";

                                  }
                                }
                              }
                              $dom->save("sip/gateways.xml");
                              ?>
                            </tbody>
                          </table>
                        </div>
                      </div>


                      <div class="col-md-3 visible-desktop"></div> <div class="col-md-7 ">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?php echo site_url('site/backendGatewayList'); ?>" class="btn btn-secondary">Cancel</a>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>





      </div>
    </div>


    <script>
      function addRow() {
        var table = document.getElementById('additionalParam');
          // var rowCount = table.rows.length;
          // var rowCount = $('#additionalParam tr').length;
          var rowCount = $('#additionalParam >tbody >tr').length;
          console.log(rowCount);
          var row = table.insertRow(rowCount+1);
          var addParam = row.insertCell(0);
          var addVal = row.insertCell(1);
        // var addParam1 = row.insertCell(2);
        // var addVal1 = row.insertCell(3);
        var removeVal = row.insertCell(2);

        var element1 = document.createElement('input');
        element1.type = 'text';
        var nameOfParamText1 = 'nameOfParamText1[]';
        element1.name = nameOfParamText1;
        element1.value = '';
        element1.placeholder = 'parameter';
        addParam.appendChild(element1);

        var element2 = document.createElement('input');
        element2.type = 'text';
        var nameOfValText1 = 'nameOfValText1[]';
        element2.name = nameOfValText1;
        element2.value = '';
        element2.placeholder = 'value';
        addVal.appendChild(element2);


        var element3 = document.createElement('input');
        element3.type = 'button';
                //console.log('rowCount',rowCount);
                var nameOfinputText3 = 'parameter'+(rowCount + 1);
                element3.name = nameOfinputText3;
                element3.value = '';
                element3.setAttribute('value', "-");
                element3.onclick = function() {
                  removeRow(nameOfinputText3);
                };
                element3.class = "";
                element3.setAttribute('class','btn btn-sm btn-info');
                removeVal.appendChild(element3);
              }




              function removeRow(nameOfinputText3) {
                try {
                  var table = document.getElementById('additionalParam');
                  console.log('nameOfinputText3',nameOfinputText3);
                  var rowCount = table.rows.length;
                  console.log('rowCount',rowCount);
                  for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    console.log('row',row);
                    var rowObj = row.cells[2].childNodes[0];
                    console.log('rowObj',rowObj);
                    if (rowObj.name == nameOfinputText3) {
                      table.deleteRow(i);
                      rowCount--;
                    }
                  }
                } catch (e) {
                  alert(e);
                }

              }



              function setRemember(valueReceived){
                if (valueReceived=="true"){
                  document.getElementById("rememberMsg").style.display="block";
                }
              }

            </script>
          </body>
          </html>
