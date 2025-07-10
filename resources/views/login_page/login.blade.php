@vite('resources/css/app.css')

<section class="min-h-screen flex">
    <!-- LEFT SIDE: Login Form -->
    <title>SOBAT ASR</title>
    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-gray-50 dark:bg-gray-900 px-6 py-8">
        <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
            <img src="{{ asset('image/logo.png') }}" class="w-28 h-28 mr-1" alt="logo">   
        </a>
        <div class="w-full max-w-md bg-white rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    SELAMAT DATANG
                </h1>

                <form class="space-y-4 md:space-y-6" action="#">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                        <input type="email" name="email" id="email" placeholder="NIP/NIK" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm text-gray-500 dark:text-gray-300">
                            <input type="checkbox" class="mr-2 w-4 h-4 rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600"> Remember me
                        </label>
                    </div>
                    <button type="submit"
                        class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        Login
                    </button>
                    <button type="button"
                        class="w-full text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">
                        Register
                    </button>
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400 text-center">
                        <a href="#" class="font-medium text-green-600 hover:underline dark:text-green-400">Forgot Password ?</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Illustration -->
  <div class="hidden md:flex w-1/2 relative items-center justify-center p-8 overflow-hidden">

  <!-- background image -->
  <img 
    src="{{ asset('image/background.jpg') }}" 
    class="absolute inset-0 w-full h-full object-cover"
    alt="Background"
  >

  <!-- green overlay semi-transparan -->
  <div class="absolute inset-0 bg-green-500 opacity-60"></div>

  <!-- logo di atas semuanya -->
  <img 
    src="{{ asset('image/logofont.png') }}" 
    class="relative max-w-md drop-shadow-xl animate-fadeIn10sOut3s"
    alt="Logo"
  >

</div>


</section>
