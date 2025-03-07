<?php require_once("../../database/connection.php"); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.0/flowbite.min.css" rel="stylesheet" />
<link rel="stylesheet" href="../output.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION["user_id"])){
    header("Location: ../home/view.php");
}


?>
<link rel="stylesheet" href="../../assets/lib/simple-notify.min.css" />
<script src="../../assets/lib/simple-notify.min.js"></script>

<body>

    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                <img class="w-16 h-16 mr-2" src="../../assets/icons/logo-no-background.png" alt="logo">

                ResourceHub
            </a>
            <div
                class="w-9/12  lg:w-1/2  bg-white rounded-lg shadow dark:border md:mt-0  xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Create An Account
                    </h1>
                    <form class="space-y-4 md:space-y-6" id="register-form" action="#">
                        <div class="grid grid-cols-2 gap-4 divide-x-2">
                            <div>
                                <div class="mt-4">
                                    <label for="email"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                    <input type="email" name="email" id="email"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="name@company.com" required="">
                                    <p class="text-xs text-red-400 mt-2 dark:text-white" id="email-error">Email Already
                                        Exists</p>

                                </div>
                                <div class="mt-4">
                                    <label for="password"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                    <input type="password" name="password" id="password" placeholder="••••••••"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        required="">
                                </div>
                                <div class="mt-4">
                                    <label for="confirm-password"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm
                                        password</label>
                                    <input type="password" name="confirm_password" id="confirm-password"
                                        placeholder="••••••••"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        required="">
                                </div>

                            </div>
                            <div>
                                <div class="ml-4 mt-4">
                                    <label for="email"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                                    <input type="text" name="username" id="email"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="John Doe" required="">
                                </div>

                                <div class="ml-4 mt-4">
                                    <label for="countries"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Account
                                        Type</label>
                                    <select id="countries" name="account_type"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option selected>Choose account type</option>
                                        <option value="ADMIN">Admin</option>
                                        <option value="STUDENT" selected>Student</option>
                                        <option value="LECTURER">Lecturer</option>

                                    </select>
                                </div>

                                <div class="ml-4 mt-4" id="student-id-input">
                                    <label for="confirm-password"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Student
                                        ID</label>
                                    <input type="confirm-password" name="student_id" id="confirm-password"
                                        placeholder="20**/***/***"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <p class="text-xs text-red-400 mt-2 dark:text-white" id="student-id-error">Student
                                        ID Already Exists</p>

                                </div>

                            </div>
                        </div>

                        <div class="w-full grid grid-cols-2">
                            <div class="flex justify-between ">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" id="terms" name="is_recurring" required
                                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800">
                                            <label 
                                                class="text-sm font-medium ml-2 text-gray-500 dark:text-gray-300">I agree to the <a href="../terms.html" class="font-medium text-blue-500">Terms and Conditions</a> </label>
                                    </div>
                                </div>

                            </div>
                           <div class="w-11/12 ml-8 ">
                           <button type="submit" onclick="register()"
                                class="text-white w-full bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 my-2">
                                Register
                            </button>
                            <p class="text-sm font-light mt-4 text-gray-500 dark:text-gray-400">
                                Already have an account? <a href="../login/view.php"
                                    class="font-medium text-primary-600 hover:underline dark:text-primary-500">Login
                                    here</a>
                            </p>
                           </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.0/flowbite.min.js"></script>

<script>
document.getElementById("email-error").style.display = "none";
document.getElementById("student-id-error").style.display = "none";
</script>

<script>
async function isAvailable(URL, Regex, value) {

    if (value.search(Regex) >= 0) {
        const response = await axios.get(URL);
        if (response.data == true) {

            return false;
        } else {

            return true;
        }


    }
}
</script>

<script>
document.getElementsByName("email")[0].addEventListener("keyup", async function() {
    const Regex = /\S+@\S+\.\S+/;
    const value = this.value;
    const URL = "../../controllers/login/existenceCheck.php?email=" + value;
    isAvailable(URL, Regex, value).then(function(result) {
        if (result == false) {
            document.getElementById("email-error").style.display = "block";
        } else {
            document.getElementById("email-error").style.display = "none";
        }

    });
})
document.getElementsByName("account_type")[0].addEventListener("change", function() {
    if (this.value == "STUDENT") {
        document.getElementById("student-id-input").style.display = "block";
    } else {
        document.getElementById("student-id-input").style.display = "none";
    }
})
document.getElementsByName("student_id")[0].addEventListener("keyup", function() {
    console.log(this.value)
    const Regex = /20[0-9][0-9]\/[A-Z][A-Z][A-Z]\/[0-9][0-9][0-9]/;
    const value = this.value;
    const URL = "../../controllers/login/existenceCheck.php?studentID=" + value;
    isAvailable(URL, Regex, value).then(function(result) {
        if (result == false) {
            document.getElementById("student-id-error").style.display = "block";
        } else {
            document.getElementById("student-id-error").style.display = "none";
        }

    });
})
</script>


<script>
document.getElementById("register-form").addEventListener("submit", function(e) {
    e.preventDefault();
})

function register() {
    const email = document.getElementsByName("email")[0].value;
    const password = document.getElementsByName("password")[0].value;
    const confirm_password = document.getElementsByName("confirm_password")[0].value;
    const username = document.getElementsByName("username")[0].value;
    const account_type = document.getElementsByName("account_type")[0].value;
    const student_id = document.getElementsByName("student_id")[0].value;
    const terms = document.getElementById("terms").checked;

 

    if ((email == "" || password == "" || confirm_password == "" || username == "" || account_type == "" || (
            account_type == "STUDENT" && student_id == ""))) {
                new Notify({
            title: 'Error',
            text: "Please fill all the fields",
            effect: 'slide',
            status: 'error',
            speed: 300,
            autoclose: true,
            autotimeout: 3000
        })
        return;
    }

    if (password != confirm_password) {
        new Notify({
            title: 'Error',
            text: "Password Mismatch",
            effect: 'slide',
            status: 'error',
            speed: 300,
            autoclose: true,
            autotimeout: 3000
        })
        return;
    }

    if (!terms) {
        new Notify({
            title: 'Error',
            text: "Please agree to the terms and conditions",
            effect: 'slide',
            status: 'error',
            speed: 300,
            autoclose: true,
            autotimeout: 3000
        })
        return;
    }

    if (document.getElementById("email-error").style.display == "block") {
        new Notify({
            title: 'Error',
            text: "Email ALready Exists",
            effect: 'slide',
            status: 'error',
            speed: 300,
            autoclose: true,
            autotimeout: 3000
        })
        return;
    }

    if (document.getElementById("student-id-error").style.display == "block") {
        new Notify({
            title: 'Error',
            text: "Student ID Already Exists",
            effect: 'slide',
            status: 'error',
            speed: 300,
            autoclose: true,
            autotimeout: 3000
        })
        return;
    }

    axios.post("../../controllers/login/login.php?register", {
        email: email,
        password: password,
        username: username,
        account_type: account_type,
        student_id: student_id
    }).then(function(response) {
        if (response.data.search("success") >= 0) {
            new Notify({
                title: 'Success',
                text: "Successfully Registered. Redirecting....",
                effect: 'slide',
                status: 'success',
                speed: 300,
                autoclose: true,
                autotimeout: 3000,

            })
            setTimeout(() => {
                window.location.href = "../login/view.php";

            }, 400);
        } else {
            new Notify({
                title: 'Error',
                text: "Registration Failed",
                effect: 'slide',
                status: 'error',
                speed: 300,
                autoclose: true,
                autotimeout: 3000
            })
        }
    }).catch(function(error) {
        new Notify({
            title: 'Error',
            text: "Registration Failed",
            effect: 'slide',
            status: 'error',
            speed: 300,
            autoclose: true,
            autotimeout: 3000
        })
    })


}
</script>