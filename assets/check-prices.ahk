; Run the script
; Set keybinds:
;   Targeting > Target Current Focus Target = V
;   System > Confirm = F
; Have market board window in very top left of screen
; Make sure the Partial Match is not checked
; Close market board window
; Focus target the market board
; Go first-person view, pressed all the way up against the market board, look up
; Press the Delete key

; This will click through all of the items in exchangeItems.php and
;   check their prices
; The point is if you are contributing data to Universalis,
;   this will refresh data for the site


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;



; Setup
#SingleInstance Force
#InstallKeybdHook
SendMode Input
DetectHiddenWindows, On
SetKeyDelay , 50, 30,
CoordMode, Pixel, Window
SetWorkingDir %A_ScriptDir%
; Basic variables
_click_time := 800
_coord_market_board  := [775, 100]
_coord_market_search := [33 , 70]
_coord_first_item    := [330, 78]


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;


; Startup hotkey
Delete::
    _id := WinExist("A")

; Setup for reading exchangeItems
_in_item      := False
_name_grabbed := False
_item_name    := ""

; Read exchangeItems to get the item name
Loop, read, ./php/exchangeItems.php
{
    Loop, parse, A_LoopReadLine, %A_Tab%
    {


        ; Load data

        ; What a stupid variable name
        _line := A_LoopReadLine

        ; Grab markers, skip lines
        ; Mark that we're in an item
        if InStr(_line, "=>")
        {
            _in_item := True
            continue
        }
        ; Mark that we're out of an item
        if InStr(_line, "],")
        {
            _in_item      := False
            _name_grabbed := False
            _item_name    := ""
            continue
        }
        ; Skip opening lines
        if !InStr(_line, ",")
            continue
        ; Skip lines if the name is gotten and we're still in an item
        if _in_item and _name_grabbed
        {
            continue
        }
        ; Skip the seal price of the item
        if _in_item and !InStr(_line, chr(34))
            continue

        ; Grab item name
        _words := StrSplit(_line, chr(34))
        _item_name    := _words[2]
        _name_grabbed := True


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;


        ; Pull up the item

        ; Open market board
        send v
        sleep _click_time
        send f
        sleep _click_time
        ; Click search box
        _click(_coord_market_search)
        _click(_coord_market_search)
        ; Type each letter of the item name
        Loop, Parse, _item_name
        {
           send %A_LoopField%
           sleep 50
        }
        ; Search item name
        sleep _click_time
        send {enter}
        sleep _click_time
        sleep _click_time

        ; Open the first item in search results
        _click(_coord_first_item)
        _click(_coord_first_item)
        ; Give it some time to grab the data
        sleep _click_time
        sleep _click_time

        ; Reset and move onto the next item
        send {esc}
        sleep _click_time
        send {esc}
        sleep _click_time
        continue
    }
}


;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;


; End the script

; Delete:: is now done, script complete
return



;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;



; Hotkeys

; Pausing
Home::Pause,Toggle

; Quitting
F1::
    ExitApp



; Basic functions

; Shorter Click function
_click(coords)
{
    ; Click the coordinates
    MouseClick,, coords[1], coords[2]

    ; Wait a moment, to show clicking position and give other actions time
    global _click_time
    sleep _click_time
    return
}