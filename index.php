<?php require('./assets/php/header.php'); ?>

<div class="min-h-screen bg-gray-800 py-6 flex flex-col justify-center sm:py-12">
  <div class="relative py-3 sm:max-w-xl sm:mx-auto">
      <div class="absolute inset-0 bg-gradient-to-r from-yellow-100 to-yellow-400 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
      <div class="relative px-4 py-10 bg-gray-700 shadow-lg sm:rounded-3xl sm:p-20">
      <div class="max-w-md mx-auto">
          <div>
          <img src="/assets/img/sealgil.png" class="h-32 mx-auto" />
          </div>
          <div class="pt-4 text-base leading-6 space-y-4 text-gray-300 sm:text-lg sm:leading-7">
          <p>
              XIV SealGil is a FFXIV tool to get market prices from
              <a href="https://universalis.app" class="underline">universalis</a>
              and tell you the grand company seal-purchasable item that
              is being actively sold for the most gil.
          </p>
          <ul class="list-disc space-y-2">
            <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                <svg class="flex-shrink-0 h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <p class="ml-2">
                Searchable selection of servers
              </p>
            </li>
            <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                <svg class="flex-shrink-0 h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <p class="ml-2">
              Live market data to determine the highest-gil item that is actively getting sold
              </p>
            </li>
            <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                <svg class="flex-shrink-0 h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <p class="ml-2">
                Orders items by their sell-price separated by whether they're selling, recommends what to spend seals on
              </p>
            </li>
          </ul>
          <p>
            Perfect for making some spare gil when you have extra company seals.
          </p>
          </div>
          <div class="pt-6 text-base leading-6 font-bold sm:text-lg sm:leading-7">
          <div class="flex mx-auto items-center justify-center">
              <x-lander.button link="/app" text="Try Now!"/>
              <x-lander.button link="/video" text="View Video"/>
          </div>
          <br><hr class="border-indigo-300"><br>
          <div class="flex mx-auto items-center justify-center">
              <input type="email" name="email" id="newsletter-email"
              class="focus:ring-yellow-600 ring ring-indigo-300 bg-gray-400 flex-1 block w-3/5 h-12 rounded-lg text-sm px-6 mx-2 text-indigo-50" placeholder="example@test.com">
              <a class="bg-gradient-to-t from-red-500 to-yellow-600 hover:from-indigo-400 hover:to-indigo-400 w-2/5 h-12 text-sm flex items-center justify-center rounded-lg text-indigo-900 cursor-pointer ring ring-indigo-300 hover:text-indigo-50">Get Early Access</a>
          </div>
          </div>
      </div>
      </div>
  </div>
</div>

<?php require('./assets/php/footer.php'); ?>