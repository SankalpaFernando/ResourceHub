import "../assets/lib/simple-notify.min.js";



function successNotification(message){
    console.log(message);
    new Notify({
        title: 'Success',
        text: message,
        effect: 'slide',
        status:'success',
        speed: 300,
        autoclose: true,
        autotimeout: 3000
    })
}

export{
    successNotification
}