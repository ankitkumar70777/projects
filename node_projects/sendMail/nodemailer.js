const express = require('express');
const mongoose = require('mongoose'); 
const app = express();
const port = 8080;
app.listen(port,(err)=>{
    if(err){
        console.log('connect error',err);
    }
    console.log('server is up');
});
app.get('/',(req,res)=>{
    console.log('welcome to home');
    res.send("welcome to home");
});

const nodeMailer = require('nodemailer');
// const smtpTransport = require('nodemailer-smtp-transport');



async function main(sendTo,sendSub,sendMsg) { 
let transporter = nodeMailer.createTransport({
    host: "us2.smtp.mailhostbox.com",
    port: 587,
    secure: false, // true for 465, false for other ports
    auth: {
        // type: 'custom',
        // method: 'MY-CUSTOM-METHOD', // forces Nodemailer to use your custom handler
      user: 'ankitkumar@voitekk.com', // generated ethereal user
      pass: 'passwd' // generated ethereal password
    },
    tls: {
   rejectUnauthorized: false,
   secureProtocol: "TLSv1_method"
 }
  });


// send mail with defined transport object
  let info = await transporter.sendMail({
    from: 'ankitkumar@voitekk.com', // sender address
    to: sendTo, // list of receivers
    subject: sendSub, // Subject line
    text: sendMsg, // plain text body
    html: sendMsg, // html body
  });

if(info.messageId){
   return `message sent successfully with id '${info.messageId}'`;
}else{
    return `error in sending msg`;
}
        
console.log("Message sent: %s", info.messageId);

}

app.get('/sendmsg',(req,res)=>{
        console.log('req.query : ', req.query);
        const sendTo = req.query.sendTo;
        const sendSub = req.query.sendSub;
        const sendMsg = req.query.sendMsg;
        console.log(sendTo,sendSub,sendMsg);

        let output = main(sendTo,sendSub,sendMsg).catch(console.error);
        console.log('output : ',output);
        output.then((data)=>{
            console.log('data : ',data);
            res.send(data);
        }).catch((err)=>{
            console.log('err : ',err);
            res.send(err);
        })
});

//main().catch(console.error);

