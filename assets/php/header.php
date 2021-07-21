<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <meta name="description" content="A FFXIV tool to get market prices from universalis.app and tell you the seal-purchasable item that is being actively sold for the most gil" />
        <meta name="keywords" content="ffxiv, ffxiv market, ffxiv universalis, grand company, company seals, grand company seals, gil, how to make gil, ffxiv gil" />
        <meta name="author" content="Ethan Henderson (zbee)" />

        <title>XIV SealGil</title>

        <script async src="https://www.googletagmanager.com/gtag/js?id=G-TB8G528SVG"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-TB8G528SVG');
        </script>
    </head>
    <body>

<div class="min-h-screen bg-gray-800 py-6 flex flex-col justify-center sm:py-12">
  <div class="relative py-3 max-w-2xl sm:max-w-xl sm:mx-auto">
      <div class="absolute inset-0 bg-gradient-to-r from-yellow-600 to-yellow-400 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
      <div class="relative px-4 py-10 bg-gray-700 shadow-lg sm:rounded-3xl sm:p-20">
      <div class="md:max-w-md mx-auto">

        <div>
            <a href="https://xiv-sealgil.herokuapp.com/"><img src="/assets/img/sealgil.png" class="h-32 mx-auto" /></a>
        </div>