<?php require('./assets/php/header.php'); ?>

<div class="min-h-screen bg-gray-800 py-6 flex flex-col justify-center sm:py-12">
  <div class="relative py-3 sm:max-w-xl sm:mx-auto">
      <div class="absolute inset-0 bg-gradient-to-r from-yellow-600 to-yellow-400 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
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
                <svg class="flex-shrink-0 h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <p class="ml-2">
                Searchable selection of worlds to find yours in
              </p>
            </li>
            <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                <svg class="flex-shrink-0 h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <p class="ml-2">
                Live market data to determine the highest-gil item that is actively getting sold
              </p>
            </li>
            <li class="flex items-start">
              <span class="h-6 flex items-center sm:h-7">
                <svg class="flex-shrink-0 h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
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
          <br><hr class="border-gray-600"><br>
          <div class="flex mx-auto items-center justify-center">
              <input type="text" id="worldSearch"
              class="focus:ring-gray-600 ring ring-gray-600 bg-gray-700 flex-1 block h-12 rounded-lg text-sm px-6 mx-2 text-gray-300" placeholder="Goblin, Hades, etc.">
          </div>
          <div class="mx-auto place-items-center justify-center bg-gray-800 rounded-lg mt-5 border-4 border-gray-600 box-border" id="searchResults">
          </div>
      </div>
      </div>
  </div>
</div>

<script src="/assets/js/serverList.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.4.6"></script>
<script>
let searchResults = $("#searchResults");
searchResults.hide();

const options = {
  keys: [
    "world"
  ],
  includeScore: true,
};

const fuse = new Fuse(serverList, options);

//When someone types in the world search
$("#worldSearch").keyup(function() {
  let results = fuse.search($(this).val());

  searchResults.empty();

  if ($(this).val() == "") {
    return searchResults.hide();
  }

  if (results.length > 0) {
    searchResults.show();

    for (let result of results) {
      searchResults.append("<div class='worldSearchResult flex mx-auto px-5 hover:bg-gray-700 py-2 px-5 cursor-pointer' data-world='" + result.item.world
      + "'><div class='w-3/6 text-gray-300'>" + result.item.world
      + "</div><div class='w-3/6 text-gray-400'>(" + result.item.group + " in " + result.item.region + ")</div></div>");
    }
  }
});

$(document).on('click', '.worldSearchResult', function(){ 
  console.log($(this).data("world"));
});
</script>

<?php require('./assets/php/footer.php'); ?>