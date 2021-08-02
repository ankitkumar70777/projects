    <!DOCTYPE html>
    <html>
    <head></head>
    <body>

        <div class="row paddingabc" style="">
            <div class="col-md-8">
                <div class="pull-right">
                    <a href="<?php echo site_url('site/backendGatewayList'); ?>" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <div class="module">
                            <div class="title">Gateway Details</div>
                            <div class="content white">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box paint color_12">
                                                    <div class="content">

                                                        <form class="form-horizontal row" method="post" action="<?php echo site_url('site/editBackendGateway'); ?>">

                                                            <div class="form-row form-group row">
                                                                <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">Gateway Name</label>
                                                                <div class="controls col-md-9">
                                                                    <input type="text" id="normal-field" class="form-control" name="gatewayName" value=""  placeholder="gateway name" required>
                                                                </div>
                                                            </div>


                                                            <div class="form-row form-group row">
                                                                <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">Gateway Ip / proxy value</label>
                                                                <div class="controls col-md-9">
                                                                    <input type="text" id="normal-field" class="form-control" name="gatewayIp" value=""  placeholder="gateway Ip" required>
                                                                </div>
                                                            </div>


                                                            <div class="form-row form-group row">
                                                                <label class="control-label col-md-3" for="normal-field" style="padding-left: 35px;">Gateway Registration</label>
                                                                <div class="controls col-md-9">
                                                                    <select name="gwRegister" id="gwRegister" id="normal-field" class="form-control" onchange="setRemember(this.value)">
                                                                        <option vlaue="true"  >true</option>
                                                                        <option vlaue="false" selected>false</option>
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
                                                                <table id="additionalParam" class="table table-striped table-hover" >
                                                                    <tr>
                                                                        <th style="display: none">parameterHide</th><!-- this is hidden -->
                                                                        <td>parameter</td>
                                                                        <th style="display: none">parameterHide</th><!-- this is hidden -->
                                                                        <td>Value</td>
                                                                        <td><button type="button" id="addPara" name="addPara" onclick="addRow()" class="btn btn-sm btn-info">
                                                                          <span class="glyphicon glyphicon-plus-sign addjson  pull-right"></span>
                                                                      </button>
                                                                  </td>
                                                              </tr>
                                                          </table>
                                                      </div>
                                                  </div>


                                                  <div class="col-md-3 visible-desktop"></div> <div class="col-md-7 ">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                    <a href="<?php echo site_url('site/backendGatewayList'); ?>" class="btn btn-secondary">Cancel</a> </div>

                                                </form>



                                            </form>

                                        </div>
                                    </div>
                                </div>
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
        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);
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


        // var element3 = document.createElement('input');
        // element3.type = 'text';
        // var nameOfParamText2 = 'nameOfParamText2[]';
        // element3.name = nameOfParamText2;
        // element3.value = 'value';
        // element3.placeholder = 'parameter';
        // element3.style.display="none";
        // addParam1.appendChild(element3);


        // var element4 = document.createElement('input');
        // element4.type = 'text';
        // var nameOfValText2 = 'nameOfValText2[]';
        // element4.name = nameOfValText2;
        // element4.value = '';
        // element4.placeholder = 'value';
        // addVal1.appendChild(element4);





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
        <?php

        ?>
    </body>
    </html>
