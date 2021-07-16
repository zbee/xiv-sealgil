<?php require('./assets/php/header.php'); ?>

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
      Sorts items by their sell-price versus seal-cost separated by whether they're selling, recommends what to spend seals on
    </p>
  </li>
</ul>
<p>
  Perfect for making some spare gil when you have extra company seals.
</p>

<div class="pt-6 text-base leading-6 font-bold sm:text-lg sm:leading-7">

  <hr class="border-gray-600"><br>

  <div class="flex mx-auto items-center justify-center">
      <input type="text" id="worldSearch"
      class="focus:ring-gray-600 ring ring-gray-600 bg-gray-700 flex-1 block h-12 rounded-lg text-sm px-6 mx-2 text-gray-300" placeholder="Goblin, Hades, etc.">
  </div>

  <div class="mx-auto place-items-center justify-center bg-gray-800 rounded-lg mt-5 border-4 border-gray-600 box-border hidden" id="searchResults"></div>
</div>

<p class="text-sm">
  <i>This works better if you contribute to universalis</i>. If you use ACT or otherwise load addons for FFXIV, 
  <a href="https://universalis.app/contribute" class="underline">click here to start contributing</a>. (ACT plugin is the simplest)
</p>


<script src="/assets/js/worldList.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.4.6"></script>
<script>
let x = 0;

//Load search results jQuery element
let searchResults = $("#searchResults");

//Search only world names and order by score
const options = {
  keys: [
    "world"
  ],
  includeScore: true,
};

//Load the fuzzy searcher
const fuse = new Fuse(serverList, options);

//When someone types in the world search
$("#worldSearch").keyup(function(e) {
  x = 0;

  //Fuzzy search the list of worlds
  let results = fuse.search($(this).val());

  //Clear out search results
  searchResults.empty();

  //Hide search results with no value
  if ($(this).val() == "") {
    return searchResults.hide();
  }

  //If there are search results, render them
  if (results.length > 0) {
    searchResults.show();

    for (let result of results) {
      if (x > 1) continue;

      searchResults.append("<div class='worldSearchResult flex mx-auto px-5 hover:bg-gray-700 py-2 px-5 cursor-pointer' data-world='" + result.item.world
      + "'><div class='w-1/3 text-gray-300'>" + result.item.world
      + "</div><div class='w-2/3 text-gray-400 text-right'>(" + result.item.group + " in " + result.item.region + ")</div></div>");

      x++;
    }

    searchResults.append("<span class='text-xs block mx-auto text-right px-2'>(press enter to select the top server, or click one)</span>");
  }
  //Hide search results with no results
  else {
    return searchResults.hide();
  }

  //Select the first world if enter was hit
  if (e.key === "Enter") {
    $("#searchResults div").first().click();
  }
});

$(document).on('click', '.worldSearchResult', function(){
  document.location.href = 'https://xiv-sealgil.herokuapp.com/results?world=' + $(this).data("world");
});
</script>

<?php require('./assets/php/footer.php'); ?>