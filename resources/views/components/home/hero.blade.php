<section class="bg-white dark:bg-gray-900 pt-0 lg:pt-0" id="home">

    	<!--Start Background Animation Body-->
		<div class="area z-0 h-screen">
			<ul class="circles">
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
			</ul>
		</div>
		<!--End Background Animation Body-->

    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-4 lg:grid-cols-12 pt-0 z-10">
        <div class="mr-auto place-self-center lg:col-span-7 flex flex-col justify-center lg:justify-start items-center lg:items-start pt-20 lg:pt-20">
            <h1 class="z-10 w-full lg:max-w-2xl mb-4 text-5xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white text-center lg:text-left bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-orange-400 lg:justify-start items-center lg:items-start pt-20 lg:pt-20">
                {{-- Payments tool for software companies --}}
                {{ $title }}
            </h1>
            <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400 text-center md:text-left">
                {{-- From checkout to global sales tax compliance, companies around the world use Flowbite to simplify their payment stack. --}}
                {{ $description }}
            </p>
            {{-- <button type="button" class="text-white bg-gradient-to-br from-pink-500 to-indigo-600 focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-pink-800 font-medium rounded-full text-lg px-6 py-3.5 text-center me-2 mb-2 transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg hover:bg-red-500">
                
                Pilih Paket
            </button>             --}}

            <div class="relative inline-flex group z-10">
                <a href="#package" title="Get package now"
                    class="relative inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-net-red-50 font-pj rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 group-hover:bg-white group-hover:text-net-red-50 border-4 border-net-red-50 hover:shadow-lg"
                    role="button">
                    <span class="mr-8">
                        Pilih Paket Sekarang
                    </span>
                    <div
                        class="mr-2 absolute right-0 flex items-center justify-center w-12 h-12 bg-white rounded-full group-hover:bg-net-red-50">
                        <i class="text-red-600 fas fa-arrow-right group-hover:text-white animate-bounce-x">

                        </i>
                    </div>
                </a>
            </div>
            


        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex z-10">
            {{-- <img src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/hero/phone-mockup.png" alt="mockup"> --}}
            <div class="relative w-full h-96 lg:h-full">
                
                <img src="{{ asset('images/hero/hero.png') }}" alt="hero" class="w-[70%] h-auto mx-auto bottom-0 right-0 absolute">
                <img src="{{ asset('images/hero/1.png') }}" alt="hero" class="absolute top-[20%] left-12 w-[35%] animate-float-1">
                <img src="{{ asset('images/hero/2.png') }}" alt="hero" class="absolute top-[43%] left-0 w-[35%] animate-float-2">
                <img src="{{ asset('images/hero/3.png') }}" alt="hero" class="absolute bottom-[20%] -left-12 w-[35%] animate-float-3">
            </div>
        </div>                
    </div>
</section>

{{-- style --}}

<style>
    
/*Start Animations*/
@-webkit-keyframes animatetop {
	from {
		top: -300px;
		opacity: 0;
	}
	to {
		top: 0;
		opacity: 1;
	}
}
@keyframes animatetop {
	from {
		top: -300px;
		opacity: 0;
	}
	to {
		top: 0;
		opacity: 1;
	}
}
@-webkit-keyframes zoomIn {
	0% {
		opacity: 0;
		-webkit-transform: scale3d(0.3, 0.3, 0.3);
		transform: scale3d(0.3, 0.3, 0.3);
	}
	50% {
		opacity: 1;
	}
}
@keyframes zoomIn {
	0% {
		opacity: 0;
		-webkit-transform: scale3d(0.3, 0.3, 0.3);
		transform: scale3d(0.3, 0.3, 0.3);
	}
	50% {
		opacity: 1;
	}
}
/*End Animations*/
/*
-- Start BackGround Animation 
*/
.area {
	width: 100%;
	position: absolute;
	z-index: 1;
    pointer-events: none;
}

.circles {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 96%;
	overflow: hidden;
}

.circles li {
	position: absolute;
	display: block;
	list-style: none;
	width: 20px;
	height: 20px;
    
	background: #fd8c0346;
	animation: animate 25s linear infinite;
	bottom: -150px;
}

.circles li:nth-child(1) {
	left: 25%;
	width: 80px;
	height: 80px;
	animation-delay: 0s;
    background: #0c25cb44;
}

.circles li:nth-child(2) {
	left: 10%;
	width: 20px;
	height: 20px;
	animation-delay: 2s;
	animation-duration: 12s;
    background: #ce102645;
}

.circles li:nth-child(3) {
	left: 70%;
	width: 20px;
	height: 20px;
	animation-delay: 4s;
    background: #ce102645;
}

.circles li:nth-child(4) {
	left: 40%;
	width: 60px;
	height: 60px;
	animation-delay: 0s;
	animation-duration: 18s;
    background: #0c25cb44;
}

.circles li:nth-child(5) {
	left: 65%;
	width: 20px;
	height: 20px;
	animation-delay: 0s;
    background: #ce102645;
}

.circles li:nth-child(6) {
	left: 75%;
	width: 110px;
	height: 110px;
	animation-delay: 3s;
    background: #fd8c0336;
}

.circles li:nth-child(7) {
	left: 35%;
	width: 150px;
	height: 150px;
	animation-delay: 7s;
    background: #0c25cb44;
}

.circles li:nth-child(8) {
	left: 50%;
	width: 25px;
	height: 25px;
	animation-delay: 15s;
	animation-duration: 45s;
    background: #fd8c0346;
}

.circles li:nth-child(9) {
	left: 20%;
	width: 15px;
	height: 15px;
	animation-delay: 2s;
	animation-duration: 35s;
    background: #ce102645;
}

.circles li:nth-child(10) {
	left: 85%;
	width: 150px;
	height: 150px;
	animation-delay: 0s;
	animation-duration: 11s;
    background: #fd8c0370;
}

@keyframes animate {
	0% {
		transform: translateY(0) rotate(0deg);
		opacity: 1;
		border-radius: 0;
	}

	100% {
		transform: translateY(-1000px) rotate(720deg);
		opacity: 0;
		border-radius: 100%;
	}
}
/*
-- End BackGround Animation 
*/
</style>

