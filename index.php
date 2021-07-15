<?php require('./assets/php/header.php'); ?>

<div class="min-h-screen bg-indigo-900 py-6 flex flex-col justify-center sm:py-12">
  <div class="relative py-3 sm:max-w-xl sm:mx-auto">
      <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-yellow-400 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
      <div class="relative px-4 py-10 bg-indigo-400 shadow-lg sm:rounded-3xl sm:p-20">
      <div class="max-w-md mx-auto">
          <div>
          <img src="/assets/img/sealgil.png" class="h-32 mx-auto" />
          </div>
          <div class="pt-4 text-base leading-6 space-y-4 text-gray-300 sm:text-lg sm:leading-7">
          <p>
              Textasync. A new form of media. Easy. Free. <br>
              Your audio transcribed with typography.
          </p>
          <ul class="list-disc space-y-2">
              <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                  <svg class="flex-shrink-0 h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
              </span>
              <p class="ml-2">
                  Review and update your text transcription while following along in a simple audio waveform
              </p>
              </li>
              <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                  <svg class="flex-shrink-0 h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
              </span>
              <p class="ml-2">
                  Choose custom colors and effects for your text
              </p>
              </li>
              <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                  <svg class="flex-shrink-0 h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
              </span>
              <p class="ml-2">Select unique animations for how the text appears, and whether it should be rendered as a full video or an overlay</p>
              </li>
          </ul>
          <p>Perfect for creating visuals for your speech, joking around with custom narration over a video, or generating fresh appearances for closed captions.</p>
          </div>
          <div class="pt-6 text-base leading-6 font-bold sm:text-lg sm:leading-7">
          <div class="flex mx-auto items-center justify-center">
              <x-lander.button link="/app" text="Try Now!"/>
              <x-lander.button link="/video" text="View Video"/>
          </div>
          <br><hr class="border-indigo-300"><br>
          <div class="flex mx-auto items-center justify-center">
              <input type="email" name="email" id="newsletter-email"
              class="focus:ring-yellow-600 ring ring-indigo-300 bg-indigo-400 flex-1 block w-3/5 h-12 rounded-lg text-sm px-6 mx-2 text-indigo-50" placeholder="example@test.com">
              <a class="bg-gradient-to-t from-red-500 to-yellow-600 hover:from-indigo-400 hover:to-indigo-400 w-2/5 h-12 text-sm flex items-center justify-center rounded-lg text-indigo-900 cursor-pointer ring ring-indigo-300 hover:text-indigo-50">Get Early Access</a>
          </div>
          </div>
      </div>
      </div>
  </div>
</div>

<?php require('./assets/php/footer.php'); ?>