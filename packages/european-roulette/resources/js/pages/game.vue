<template>
  <div class="d-flex flex-column fill-height py-3">
    <div class="message-container d-flex justify-center align-center">
      <game-message :message="message" />
    </div>
    <div class="d-flex flex-sm-row flex-column justify-center fill-height align-center">
      <div>
        <div v-click-outside="onClickOutsideRoulette" class="roulette">
          <canvas ref="roulette" width="480" height="480" @click="rouletteBet" @mousemove="rouletteMouse" />
          <object class="roulette-source">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 829.1 829.3">
              <g ref="wheel" style="transform-origin:center;transform:rotate(0rad);">
                <line class="st0" x1="450.6" y1="3.8" x2="450.5" y2="5.3" />
                <line class="st1" x1="380.8" y1="5.2" x2="380.6" y2="3.8" />
                <line class="st2" x1="311.9" y1="16.8" x2="311.6" y2="15.8" />
                <line class="st3" x1="245.2" y1="40.4" x2="245.1" y2="40.3" />
                <line class="st3" x1="184.8" y1="74.2" x2="184.1" y2="73.3" />
                <line class="st3" x1="130.2" y1="118.3" x2="130.1" y2="118.3" />
                <line class="st3" x1="84.8" y1="169.8" x2="84.1" y2="169.3" />
                <line class="st4" x1="48.3" y1="228.6" x2="46.6" y2="227.8" />
                <line class="st5" x1="519.6" y1="15.8" x2="519.3" y2="17.1" />
                <line class="st3" x1="585.1" y1="40.3" x2="585.1" y2="40.4" />
                <line class="st6" x1="645.6" y1="73.8" x2="645.4" y2="74.1" />
                <line class="st3" x1="699.1" y1="117.3" x2="699" y2="117.4" />
                <line class="st3" x1="745.1" y1="169.3" x2="745.1" y2="169.3" />
                <line class="st7" x1="783.6" y1="228.8" x2="782.4" y2="229.4" />
                <line class="st3" x1="808.1" y1="292.3" x2="808" y2="292.3" />
                <line class="st3" x1="818.1" y1="501.3" x2="817.6" y2="501.2" />
                <line class="st8" x1="797.6" y1="567.8" x2="797.5" y2="567.7" />
                <line class="st3" x1="766.1" y1="630.3" x2="766.1" y2="630.2" />
                <line class="st9" x1="725.6" y1="687.8" x2="724.4" y2="686.7" />
                <line class="st3" x1="674.1" y1="735.3" x2="674" y2="735.1" />
                <line class="st10" x1="617.6" y1="775.8" x2="616.7" y2="774.1" />
                <line class="st11" x1="552.6" y1="804.8" x2="552.2" y2="803.4" />
                <line class="st12" x1="486.6" y1="821.8" x2="486.4" y2="820.6" />
                <line class="st13" x1="415.1" y1="828.8" x2="415.1" y2="826.8" />
                <line class="st14" x1="344.8" y1="820.8" x2="344.6" y2="821.8" />
                <line class="st3" x1="277.2" y1="803.1" x2="277.1" y2="803.3" />
                <line class="st10" x1="212.4" y1="773.4" x2="211.6" y2="774.8" />
                <line class="st15" x1="155.6" y1="734.6" x2="154.6" y2="735.8" />
                <line class="st16" x1="104.5" y1="685.2" x2="102.6" y2="686.8" />
                <line class="st3" x1="64.2" y1="630.2" x2="64.1" y2="630.3" />
                <line class="st17" x1="33.4" y1="569.1" x2="31.6" y2="569.8" />
                <line class="st3" x1="22.3" y1="292.3" x2="22.1" y2="292.3" />

                <path ref="rouletteCell0" class="st19" d="M450.5,5.3l-9.6,106.9l-0.1,0.6c0,0-0.1,0-0.2,0c-8.4-0.7-16.9-1.1-25.5-1.1c-8.5,0-17,0.4-25.3,1 c0,0-0.1,0-0.2,0l0-0.6l-8.8-107c11.3-0.9,22.8-1.4,34.4-1.4C427,3.8,438.8,4.3,450.5,5.3z" />
                <path ref="rouletteCell1" class="st20" d="M519.3,17.1l-27.2,103.9l-0.2,0.6c0,0,0,0,0,0c-16.5-4.3-33.6-7.3-51.1-8.7l0.1-0.6l9.6-106.9 C474.1,7.3,497,11.3,519.3,17.1z" />
                <path ref="rouletteCell2" class="st18" d="M585.1,40.4l-44.7,98.4c-15.5-7-31.7-12.8-48.5-17.2l0.2-0.6l27.2-103.9C542,23,564,30.8,585.1,40.4z" />
                <path ref="rouletteCell3" class="st20" d="M645.4,74.1l-60.3,89.3l-0.2,0.2c0,0,0,0,0,0c-14-9.5-28.9-17.8-44.5-24.9l44.7-98.3 C606.2,50,626.3,61.3,645.4,74.1z" />
                <path ref="rouletteCell4" class="st18" d="M699,117.4l-74.5,78.2c-12.3-11.7-25.5-22.4-39.6-31.9l0.2-0.2l60.3-89.3C664.5,87.1,682.4,101.5,699,117.4z" />
                <path ref="rouletteCell5" class="st20" d="M745.1,169.3l-86.7,64.4c-10.2-13.6-21.5-26.4-33.8-38.1l74.5-78.2C715.8,133.3,731.2,150.7,745.1,169.3z" />
                <path ref="rouletteCell6" class="st18" d="M782.4,229.4l-95.9,48.2l-0.6,0.3c0,0,0-0.1,0-0.1c-7.9-15.5-17.1-30.3-27.5-44.1l86.7-64.4 C759.2,188.2,771.7,208.3,782.4,229.4z" />
                <path ref="rouletteCell7" class="st20" d="M808,292.3l-103.2,32c-5-16.1-11.4-31.6-18.9-46.4l0.6-0.3l95.9-48.2C792.6,249.5,801.1,270.5,808,292.3z" />
                <path ref="rouletteCell8" class="st18" d="M823.1,361.3L716,375.2c-2.3-17.5-6.1-34.5-11.2-50.8l103.2-32C814.9,314.5,820,337.6,823.1,361.3z" />
                <path ref="rouletteCell9" class="st20" d="M826.6,415.3c0,5.5-0.1,11-0.3,16.4L719.1,427l-0.6,0c0-0.1,0-0.1,0-0.2c0.1-3.8,0.2-7.7,0.2-11.5 c0-13.6-0.9-27-2.6-40.1l107.1-13.9C825.4,378.9,826.6,397,826.6,415.3z" />
                <path ref="rouletteCell10" class="st18" d="M826.3,431.7c-0.9,23.7-3.9,46.9-8.7,69.5l-105.6-22.8c3.5-16.7,5.7-33.8,6.4-51.4l0.6,0L826.3,431.7z" />
                <path ref="rouletteCell11" class="st20" d="M817.6,501.2c-4.9,22.9-11.7,45.2-20.2,66.5l-100-40.2l-0.3-0.1c0,0,0-0.1,0-0.1 c6.2-15.7,11.2-32.1,14.8-48.9L817.6,501.2z" />
                <path ref="rouletteCell12" class="st18" d="M797.5,567.7c-8.7,21.8-19.2,42.7-31.4,62.5l-92-56.6c9-14.6,16.7-30.1,23.2-46.2l0.3,0.1L797.5,567.7z" />
                <path ref="rouletteCell13" class="st20" d="M766.1,630.2c-12.3,20-26.2,38.9-41.7,56.5l-80.3-70.8l-0.7-0.6c0,0,0,0,0,0c11.3-12.9,21.6-26.9,30.7-41.6 L766.1,630.2z" />
                <path ref="rouletteCell14" class="st18" d="M724.4,686.7c-15.4,17.5-32.3,33.7-50.4,48.4l-67.8-84.1c13.4-10.9,25.8-22.8,37.2-35.8l0.7,0.6L724.4,686.7z" />
                <path ref="rouletteCell15" class="st20" d="M674,735.1c-17.9,14.5-37.1,27.6-57.3,39l-52.5-93.4l-0.4-0.8c15-8.4,29.2-18.1,42.5-28.9L674,735.1z" />
                <path ref="rouletteCell16" class="st18" d="M616.7,774.1c-20.5,11.5-42.1,21.4-64.5,29.3l-35.7-101.3l-0.2-0.6h0c16.5-5.8,32.2-13,47.3-21.4 c0.1,0,0.1-0.1,0.2-0.1l0.4,0.8L616.7,774.1z" />
                <path ref="rouletteCell17" class="st20" d="M552.2,803.4c-21.2,7.5-43.1,13.3-65.7,17.2l-18.7-105.7l-0.1-0.7c16.7-2.9,33-7.2,48.6-12.7l0.2,0.6 L552.2,803.4z" />
                <path ref="rouletteCell18" class="st18" d="M486.4,820.6c-23.2,4.1-47,6.2-71.3,6.2v-108c17.9,0,35.4-1.6,52.4-4.5c0,0,0.1,0,0.1,0l0.1,0.7L486.4,820.6z" />
                <path ref="rouletteCell19" class="st20" d="M415.1,718.8v108c-24,0-47.5-2.1-70.3-6L363.2,715l0.1-0.7c0,0,0.1,0,0.1,0 C380.2,717.3,397.5,718.8,415.1,718.8z" />
                <path ref="rouletteCell20" class="st18" d="M363.3,714.4l-0.1,0.7l-18.4,105.8c-23.3-4-45.9-10-67.6-17.7l36.2-101.8C329.5,707,346.2,711.4,363.3,714.4z" />
                <path ref="rouletteCell21" class="st20" d="M313.4,701.3l-36.2,101.8c-22.6-8-44.2-18-64.8-29.6l52.9-93.2l0.4-0.8c0.1,0,0.1,0.1,0.2,0.1 C281,688.2,296.9,695.4,313.4,701.3z" />
                <path ref="rouletteCell22" class="st18" d="M265.8,679.5l-0.4,0.8l-52.9,93.2c-20-11.4-39-24.4-56.8-38.8l67.6-83.1l0.6-0.7c0,0,0,0,0.1,0.1 C236.9,661.6,251,671.1,265.8,679.5z" />
                <path ref="rouletteCell23" class="st20" d="M223.8,650.8l-0.6,0.7l-67.6,83.1c-18.4-15-35.5-31.5-51.1-49.5l80.7-69.9l0.9-0.8c0,0.1,0.1,0.1,0.1,0.2 C197.7,627.7,210.3,639.8,223.8,650.8z" />
                <path ref="rouletteCell24" class="st18" d="M186.1,614.4l-0.9,0.8l-80.7,69.9c-14.9-17.1-28.4-35.5-40.3-54.9l92.1-56.4 C165.1,588.2,175.1,601.8,186.1,614.4z" />
                <path ref="rouletteCell25" class="st20" d="M156.3,573.8l-92.1,56.4c-11.9-19.4-22.2-39.8-30.8-61.2l99.3-40.5l0.8-0.3 C139.7,544.1,147.4,559.4,156.3,573.8z" />
                <path ref="rouletteCell26" class="st18" d="M133.4,528.2l-0.8,0.3l-99.3,40.5c-8.8-21.8-15.8-44.5-20.7-67.9l105.6-22.8c3.6,17.1,8.7,33.7,15,49.5 C133.3,528,133.3,528.1,133.4,528.2z" />
                <path ref="rouletteCell27" class="st20" d="M118.2,478.4L12.6,501.2c-4.9-23.2-7.9-47-8.7-71.5l107.3-3.8l0.6,0c0,0.1,0,0.2,0,0.2 C112.5,443.9,114.6,461.4,118.2,478.4z" />
                <path ref="rouletteCell28" class="st18" d="M111.6,415.3c0,3.5,0.1,7.1,0.2,10.6l-0.6,0L3.9,429.7c-0.2-4.8-0.3-9.6-0.3-14.5c0-18.3,1.2-36.3,3.5-54 l107.1,13.9C112.5,388.3,111.6,401.7,111.6,415.3z" />
                <path ref="rouletteCell29" class="st20" d="M125.5,324.3c-5.1,16.4-8.9,33.4-11.2,50.8L7.2,361.3c3.1-23.7,8.2-46.7,15.2-68.9L125.5,324.3z" />
                <path ref="rouletteCell30" class="st18" d="M144.5,277.8c0,0.1-0.1,0.2-0.1,0.2c-7.5,14.8-13.8,30.3-18.9,46.3l-103.2-32c6.9-22.1,15.6-43.4,26-63.7 l95.4,48.8L144.5,277.8z" />
                <path ref="rouletteCell31" class="st20" d="M171.4,234.3c-10.1,13.6-19.2,28.2-26.9,43.5l-0.8-0.4l-95.4-48.8c10.5-20.7,22.8-40.4,36.5-58.8L171.4,234.3z" />
                <path ref="rouletteCell32" class="st18" d="M205,196.3c-12.2,11.7-23.4,24.4-33.6,38l-86.6-64.6c13.7-18.4,28.9-35.6,45.4-51.4L205,196.3z" />
                <path ref="rouletteCell33" class="st20" d="M245.4,163.7c-14.4,9.7-27.9,20.6-40.4,32.6l-74.8-77.9c16.9-16.2,35.1-31,54.6-44.1L245.4,163.7z" />
                <path ref="rouletteCell34" class="st18" d="M289.7,138.8c-15.5,7.1-30.3,15.4-44.3,24.8l-60.6-89.4c19-12.9,39.3-24.3,60.4-33.9L289.7,138.8z" />
                <path ref="rouletteCell35" class="st20" d="M338.9,121.4c-17,4.4-33.5,10.3-49.2,17.4l-44.5-98.4c21.3-9.7,43.6-17.6,66.7-23.6l26.8,104L338.9,121.4z" />
                <path ref="rouletteCell36" class="st18" d="M389.7,112.8c-17.4,1.4-34.3,4.4-50.8,8.6c0,0,0,0,0,0l-0.2-0.6l-26.8-104c22.3-5.8,45.3-9.7,68.8-11.6 l8.8,107L389.7,112.8z" />

                <radialGradient id="SVGID_1_" cx="345.105" cy="158.11" r="38.018" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st21" d="M376.8,637.1l-13.5,77.3c-17.2-2.9-33.9-7.3-49.9-13.1l26.3-73.8C351.6,631.7,364,634.9,376.8,637.1z" />

                <radialGradient id="SVGID_2_" cx="389.2401" cy="151.1" r="34.1899" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st22" d="M415.1,640.3v78.5c-17.6,0-34.9-1.5-51.7-4.4c0,0-0.1,0-0.1,0l13.5-77.3c12.3,2.1,24.9,3.2,37.8,3.2 L415.1,640.3L415.1,640.3z" />

                <radialGradient id="SVGID_3_" cx="441.3951" cy="151.2299" r="34.4024" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st23" d="M467.7,714.2C467.6,714.2,467.6,714.2,467.7,714.2c-17.1,3-34.6,4.5-52.5,4.5v-78.5c13.3,0,26.2-1.2,38.8-3.4 L467.7,714.2z" />

                <radialGradient id="SVGID_4_" cx="485.135" cy="158.27" r="37.8114" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st24" d="M516.3,701.5c-15.7,5.5-31.9,9.8-48.6,12.7L454,636.8c12.4-2.2,24.5-5.4,36.2-9.5L516.3,701.5z" />

                <radialGradient id="SVGID_5_" cx="526.965" cy="172.61" r="41.1411" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st25" d="M563.8,679.9c-0.1,0-0.1,0.1-0.2,0.1c-15,8.4-30.8,15.6-47.3,21.4h0l-26.1-74.2c12.2-4.3,23.9-9.7,35-15.9 L563.8,679.9z" />

                <radialGradient id="SVGID_6_" cx="565.695" cy="194.15" r="42.8478" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st26" d="M606.2,651.1c-13.3,10.8-27.5,20.4-42.5,28.9l-38.6-68.6c11.2-6.3,21.7-13.5,31.6-21.5L606.2,651.1z" />

                <radialGradient id="SVGID_7_" cx="600.1" cy="221.885" r="43.6186" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st27" d="M643.4,615.3c-11.4,13-23.8,24.9-37.2,35.8l-49.4-61.3c9.9-8.1,19.2-17,27.6-26.6L643.4,615.3z" />

                <radialGradient id="SVGID_8_" cx="629.235" cy="255.21" r="43.1912" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st28" d="M674.1,573.6c-9,14.8-19.3,28.7-30.7,41.6c0,0,0,0,0,0l-59-52c8.4-9.6,16-19.9,22.7-30.8L674.1,573.6z" />

                <radialGradient id="SVGID_9_" cx="652.165" cy="293.21" r="41.6077" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st29" d="M697.3,527.4c-6.4,16.1-14.2,31.6-23.2,46.2l-67-41.3c6.7-10.9,12.4-22.4,17.2-34.4L697.3,527.4z" />

                <radialGradient id="SVGID_10_" cx="668.165" cy="334.455" r="38.7488" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st30" d="M712.1,478.4c-3.5,16.9-8.5,33.2-14.8,48.9c0,0,0,0.1,0,0.1l-73-29.4c4.6-11.6,8.3-23.8,11-36.3L712.1,478.4z" />

                <radialGradient id="SVGID_11_" cx="676.825" cy="378.07" r="35.2201" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st31" d="M718.4,427c-0.7,17.5-2.8,34.7-6.4,51.4l-76.8-16.6c2.6-12.4,4.2-25.2,4.7-38.2L718.4,427z" />

                <radialGradient id="SVGID_12_" cx="678.435" cy="427.95" r="33.8175" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st32" d="M718.6,415.3c0,3.9-0.1,7.7-0.2,11.5c0,0.1,0,0.1,0,0.2l-78.5-3.4c0.1-2.9,0.2-5.8,0.2-8.8 c0-10-0.7-19.8-1.9-29.5l77.8-10.1C717.8,388.3,718.6,401.7,718.6,415.3z" />

                <radialGradient id="SVGID_13_" cx="672.985" cy="474.22" r="37.2798" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st33" d="M716,375.2l-77.8,10.1c-1.7-13-4.5-25.5-8.3-37.7l74.8-23.2C709.9,340.7,713.7,357.7,716,375.2z" />

                <radialGradient id="SVGID_14_" cx="660.365" cy="516.29" r="39.9169" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st34" d="M704.8,324.3L630,347.6c-3.7-12-8.4-23.5-14-34.5l69.9-35.1C693.4,292.7,699.7,308.3,704.8,324.3z" />

                <radialGradient id="SVGID_15_" cx="640.745" cy="555.665" r="42.4768" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st35" d="M685.8,277.9L615.9,313c-5.8-11.5-12.6-22.5-20.3-32.8l62.7-46.6C668.7,247.5,677.9,262.3,685.8,277.9
                  C685.8,277.9,685.8,277.9,685.8,277.9z"
                />

                <radialGradient id="SVGID_16_" cx="614.54" cy="591.13" r="43.0913" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st36" d="M658.4,233.7l-62.7,46.6c-7.5-10.1-15.8-19.5-24.9-28.2l53.8-56.5C636.8,207.3,648.2,220.1,658.4,233.7z" />

                <radialGradient id="SVGID_17_" cx="582.925" cy="621.2" r="42.9118" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st37" d="M624.5,195.6L570.7,252c-9.1-8.7-18.9-16.7-29.4-23.8l43.6-64.5C599,173.2,612.3,183.9,624.5,195.6z" />

                <radialGradient id="SVGID_18_" cx="546.56" cy="645.575" r="41.6642" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st38" d="M584.9,163.7l-43.6,64.5c-10.5-7.1-21.5-13.4-33.1-18.6l32.2-70.8C556,145.8,570.8,154.1,584.9,163.7
                  C584.9,163.6,584.9,163.6,584.9,163.7z"
                />

                <radialGradient id="SVGID_19_" cx="506.315" cy="663.485" r="39.3389" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="4.020000e-02" style="stop-color:#333333" />
                  <stop offset="0.4686" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st39" d="M540.4,138.7l-32.2,70.8c-11.5-5.3-23.5-9.6-36-12.8l19.7-75.1C508.7,125.9,524.9,131.7,540.4,138.7z" />

                <radialGradient id="SVGID_20_" cx="462.9" cy="674.27" r="36.0615" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st40" d="M491.9,121.6l-19.7,75.1c-12.4-3.3-25.2-5.5-38.4-6.6l7-77.2C458.3,114.3,475.4,117.3,491.9,121.6
                  C491.9,121.6,491.9,121.6,491.9,121.6z"
                />

                <radialGradient id="SVGID_21_" cx="415.24" cy="678.115" r="33.0715" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#4C8841" />
                  <stop offset="0.3139" style="stop-color:#337C2E" />
                  <stop offset="1" style="stop-color:#006406" />
                </radialGradient>
                <path class="st41" d="M440.8,112.8l-7,77.2c-6.3-0.5-12.8-0.8-19.2-0.8c-6.3,0-12.5,0.3-18.6,0.8l-6.4-77.2c0,0,0.1,0,0.2,0
                  c8.3-0.7,16.8-1,25.3-1c8.6,0,17.1,0.4,25.5,1.1C440.7,112.8,440.8,112.8,440.8,112.8z"
                />

                <radialGradient id="SVGID_22_" cx="367.455" cy="674.43" r="35.8056" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="4.020000e-02" style="stop-color:#333333" />
                  <stop offset="0.4686" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st42" d="M396,190c-13,1-25.6,3.2-37.9,6.4l-19.3-75c0,0,0,0,0,0c16.4-4.3,33.4-7.1,50.8-8.6L396,190z" />

                <radialGradient id="SVGID_23_" cx="323.92" cy="663.66" r="39.3953" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st43" d="M358.2,196.4c-12.7,3.3-24.9,7.6-36.6,12.9l-31.8-70.5c15.7-7.1,32.2-13,49.2-17.4L358.2,196.4z" />

                <radialGradient id="SVGID_24_" cx="283.4401" cy="645.725" r="41.4058" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="4.020000e-02" style="stop-color:#333333" />
                  <stop offset="0.4686" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st44" d="M321.5,209.3c-11.5,5.2-22.6,11.4-33,18.5l-43.2-64.1c14-9.5,28.8-17.8,44.3-24.8L321.5,209.3z" />

                <radialGradient id="SVGID_25_" cx="246.77" cy="621.17" r="43.0022" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st45" d="M288.5,227.8c-10.7,7.2-20.7,15.4-30,24.3L205,196.3c12.5-12,26-22.9,40.4-32.6L288.5,227.8z" />

                <radialGradient id="SVGID_26_" cx="214.975" cy="590.68" r="42.8088" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="4.020000e-02" style="stop-color:#333333" />
                  <stop offset="0.4686" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st46" d="M258.5,252.1c-9.1,8.7-17.5,18.2-25,28.4l-62.1-46.1c10.1-13.6,21.4-26.3,33.6-38L258.5,252.1z" />

                <radialGradient id="SVGID_27_" cx="189.005" cy="555.37" r="41.9973" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st47" d="M233.5,280.4c-7.6,10.2-14.4,21.1-20.1,32.6l-68.9-35.2c7.8-15.3,16.8-29.8,26.9-43.5L233.5,280.4z" />

                <radialGradient id="SVGID_28_" cx="169.425" cy="516.49" r="39.6096" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="4.020000e-02" style="stop-color:#333333" />
                  <stop offset="0.4686" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st48" d="M213.4,313c-5.5,11-10.2,22.4-14,34.3l-73.9-23c5-16,11.4-31.5,18.9-46.3c0-0.1,0.1-0.2,0.1-0.2L213.4,313z" />

                <radialGradient id="SVGID_29_" cx="156.84" cy="474.285" r="36.991" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st49" d="M199.4,347.3c-3.8,12.2-6.6,24.9-8.3,37.9l-76.8-10c2.3-17.5,6.1-34.5,11.2-50.8L199.4,347.3z" />

                <radialGradient id="SVGID_30_" cx="151.355" cy="428.5201" r="33.3083" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="4.020000e-02" style="stop-color:#333333" />
                  <stop offset="0.4686" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st50" d="M189.1,414.8c0,2.8,0,5.5,0.2,8.3l-77.5,2.8c-0.1-3.5-0.2-7-0.2-10.6c0-13.6,0.9-27,2.6-40.1l76.8,10
                  C189.8,394.8,189.1,404.7,189.1,414.8z"
                />

                <radialGradient id="SVGID_31_" cx="152.955" cy="378.325" r="35.0492" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st51" d="M194.1,462l-75.9,16.4c-3.6-17-5.8-34.4-6.4-52.3c0-0.1,0-0.2,0-0.2l77.5-2.8
                  C189.8,436.4,191.4,449.4,194.1,462z"
                />

                <radialGradient id="SVGID_32_" cx="161.78" cy="333.925" r="38.6959" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st52" d="M205.3,498.9l-72,29.4c0-0.1-0.1-0.2-0.1-0.3c-6.4-15.9-11.4-32.5-15-49.6l75.9-16.4
                  C196.8,474.7,200.6,487,205.3,498.9z"
                />

                <radialGradient id="SVGID_33_" cx="178.025" cy="292.705" r="41.2262" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st53" d="M222.7,533.2l-66.4,40.7c-8.9-14.4-16.6-29.7-22.9-45.6l72-29.4C210.2,510.8,216,522.3,222.7,533.2z" />

                <radialGradient id="SVGID_34_" cx="200.68" cy="255.24" r="42.5511" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st54" d="M245.1,563.4l-58.9,51c-11-12.7-21-26.2-29.8-40.6l66.4-40.7C229.3,543.9,236.8,554,245.1,563.4z" />

                <radialGradient id="SVGID_35_" cx="229.595" cy="221.925" r="43.5852" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st55" d="M273,590.3l-49.3,60.5c-13.5-11-26.1-23.1-37.5-36.2c0,0-0.1-0.1-0.1-0.2l58.9-51
                  C253.6,573.1,263,582.1,273,590.3z"
                />

                <radialGradient id="SVGID_36_" cx="264.08" cy="194.14" r="42.5244" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" style="stop-color:#333333" />
                  <stop offset="0.4464" style="stop-color:#1A1A1A" />
                  <stop offset="1" style="stop-color:#000000" />
                </radialGradient>
                <path class="st56" d="M304.4,611.5l-38.6,68c-14.8-8.4-28.8-18-41.9-28.6c0,0,0,0-0.1-0.1l49.3-60.5
                  C282.9,598.2,293.3,605.3,304.4,611.5z"
                />

                <radialGradient id="SVGID_37_" cx="302.71" cy="172.625" r="41.1094" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0" class="whell-ic-cell-red s5" />
                  <stop offset="0.2342" class="whell-ic-cell-red s4" />
                  <stop offset="0.5295" class="whell-ic-cell-red s3" />
                  <stop offset="0.7941" class="whell-ic-cell-red s2" />
                  <stop offset="1" class="whell-ic-cell-red s1" />
                </radialGradient>
                <path class="st57" d="M339.7,627.5l-26.3,73.8c-16.5-5.9-32.4-13.1-47.4-21.7c-0.1,0-0.1-0.1-0.2-0.1l38.6-68
                  C315.6,617.8,327.4,623.2,339.7,627.5z"
                />

                <radialGradient id="SVGID_38_" cx="414.64" cy="414.27" r="225.5" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop class="circle-inner-gradient g1" offset="0.8945" />
                  <stop class="circle-inner-gradient g2" offset="0.901" />
                  <stop class="circle-inner-gradient g3" offset="0.9166" />
                  <stop class="circle-inner-gradient g4" offset="0.9387" />
                  <stop class="circle-inner-gradient g5" offset="1" />
                </radialGradient>
                <path class="st58" d="M638.2,385.3c-1.7-13-4.5-25.5-8.3-37.7c-3.7-12-8.4-23.5-14-34.5c-5.8-11.5-12.6-22.5-20.3-32.8
                  c-7.5-10.1-15.8-19.5-24.9-28.2c-9.1-8.7-18.9-16.7-29.4-23.8c-10.5-7.1-21.5-13.4-33.1-18.6c-11.5-5.3-23.5-9.6-36-12.8
                  c-12.4-3.3-25.2-5.5-38.4-6.6c-6.3-0.5-12.8-0.8-19.2-0.8c-6.3,0-12.5,0.3-18.6,0.8c-13,1-25.6,3.2-37.9,6.4
                  c-12.7,3.3-24.9,7.6-36.6,12.9c-11.5,5.2-22.6,11.4-33,18.5c-10.7,7.2-20.7,15.4-30,24.3c-9.1,8.7-17.5,18.2-25,28.4
                  c-7.6,10.2-14.4,21.1-20.1,32.6c-5.5,11-10.2,22.4-14,34.3c-3.8,12.2-6.6,24.9-8.3,37.9c-1.3,9.7-1.9,19.6-1.9,29.6
                  c0,2.8,0,5.5,0.2,8.3c0.5,13.3,2.1,26.3,4.8,38.9c2.7,12.7,6.5,25,11.3,36.9c4.8,12,10.6,23.4,17.3,34.3
                  c6.6,10.7,14.1,20.8,22.4,30.2c8.5,9.7,17.9,18.7,28,26.9c9.8,7.9,20.3,15,31.3,21.3c11.2,6.3,23,11.7,35.3,16
                  c12,4.2,24.4,7.5,37.1,9.6c12.3,2.1,24.9,3.2,37.8,3.2h0.5c13.3,0,26.2-1.2,38.8-3.4c12.4-2.2,24.5-5.4,36.2-9.5
                  c12.2-4.3,23.9-9.7,35-16c11.2-6.3,21.7-13.5,31.6-21.5c9.9-8.1,19.2-17,27.6-26.6c8.4-9.6,16-19.9,22.7-30.8
                  c6.7-10.9,12.4-22.4,17.2-34.3c4.6-11.6,8.3-23.8,11-36.3c2.6-12.4,4.2-25.2,4.7-38.2c0.1-2.9,0.2-5.8,0.2-8.8
                  C640.1,404.8,639.5,394.9,638.2,385.3z M414.6,511.8c-53.6,0-97-43.4-97-97s43.4-97,97-97s97,43.4,97,97S468.2,511.8,414.6,511.8z"
                />
                <circle class="st59" cx="414.6" cy="414.8" r="97" />
                <path class="st60" d="M408.6,42v-1.5c0-1.3,0.2-2.3,0.6-3c0.4-0.8,1-1.4,1.8-1.9c0.3-0.2,0.6-0.3,0.9-0.4c0.3-0.1,0.7-0.2,1.1-0.3
                  s0.9-0.1,1.5-0.1c0.6,0,1.2,0,2,0c1,0,1.8,0,2.5,0.1c0.7,0,1.2,0.1,1.7,0.2c0.5,0.1,0.9,0.3,1.2,0.5c0.3,0.2,0.7,0.4,1,0.7
                  c0.8,0.8,1.3,1.8,1.4,3.1c0,0.3,0.1,0.6,0.1,0.9c0,0.2,0,0.8,0,1.7l0,25c0,0.7,0,1.2,0,1.6s0,0.8-0.1,1.1c0,0.3-0.1,0.5-0.1,0.7
                  c-0.1,0.2-0.1,0.4-0.2,0.6c-0.3,0.7-0.7,1.2-1.3,1.7c-0.6,0.5-1.2,0.8-2,1c-0.4,0.1-1,0.2-1.6,0.3c-0.6,0.1-1.5,0.1-2.6,0.1
                  c-0.8,0-1.5,0-2,0c-0.6,0-1.1-0.1-1.5-0.1c-0.4-0.1-0.8-0.2-1.1-0.3c-0.3-0.1-0.6-0.2-0.9-0.4c-0.8-0.5-1.4-1.1-1.8-1.9
                  c-0.4-0.8-0.6-1.8-0.5-3V67L408.6,42z M417.6,43v-0.8c0-0.7-0.1-1.3-0.3-1.6c-0.2-0.3-0.5-0.4-0.9-0.4c-0.6,0-0.9,0.3-1.1,1
                  c0,0.2-0.1,0.4-0.1,0.6c0,0.3,0,0.7,0,1.2l0,23.1c0,0.7,0,1.1,0,1.4c0,0.2,0,0.4,0.1,0.6c0.2,0.6,0.5,0.9,1.1,0.9
                  c0.8,0,1.2-0.7,1.2-2.1v-0.8L417.6,43z"
                />
                <path class="st60" d="M463.2,60.9l-1.2,8.8l0,0.3c-0.2,1.3,0.1,2,0.9,2.1s1.3-0.5,1.4-1.8l0-0.2l1.2-8.8l0.1-0.4
                  c0.2-1.2-0.3-1.8-1.5-1.9l-0.3-0.1l-1.7-0.2l0.7-5.2l1.4,0.2c1,0.1,1.7,0.1,2-0.1c0.3-0.2,0.5-0.8,0.6-1.8l0.1-0.5l0.7-5.2l0.1-0.6
                  c0.2-1.2-0.2-1.8-0.9-1.9c-0.8-0.1-1.3,0.5-1.4,1.8l-0.1,0.5l-0.8,5.8l-6.6-0.9l0.8-6.2l0.1-1c0.2-1.4,0.4-2.5,0.8-3.2
                  s0.9-1.3,1.7-1.8c0.8-0.5,2-0.7,3.3-0.6c0.2,0,0.6,0,1,0.1c0.5,0.1,1,0.1,1.8,0.2c0.9,0.1,1.7,0.3,2.4,0.4c0.7,0.1,1.2,0.3,1.7,0.4
                  c0.5,0.2,0.8,0.4,1.1,0.6s0.6,0.5,0.9,0.8c0.5,0.6,0.8,1.3,0.9,2c0.1,0.7,0.1,1.7-0.1,3.1l-0.1,0.8l-0.7,5.2l-0.1,0.6
                  c-0.2,1.4-0.5,2.5-1,3.1c-0.5,0.7-1.3,1.2-2.5,1.6c1,0.7,1.7,1.5,2,2.3s0.3,2,0.1,3.6l-1.1,8.3l-0.1,0.9c-0.3,1.6-0.6,2.7-1,3.5
                  s-1.1,1.3-2.1,1.7c-0.3,0.1-0.6,0.2-0.9,0.3c-0.3,0.1-0.7,0.1-1.1,0.1c-0.4,0-0.9,0-1.5-0.1c-0.6-0.1-1.3-0.1-2.1-0.3
                  c-1.7-0.2-2.9-0.5-3.7-0.8c-0.8-0.3-1.5-0.7-2-1.2c-0.6-0.6-1-1.3-1.1-2.1c-0.2-0.7-0.2-1.7,0-2.9l0.1-0.6l1.3-9.8L463.2,60.9z"
                />
                <path class="st60" d="M473.7,72.7l11.7-15.9c0.5-0.6,0.7-1,0.8-1.2l0.2-1.1l1-5.3l0.1-0.4c0.2-1.1-0.1-1.8-0.8-1.9
                  c-0.8-0.1-1.3,0.3-1.5,1.3l-0.1,0.4l-1.2,6.2l-6.5-1.3l1.3-6.9l0.2-1c0.2-1.2,0.7-2.2,1.2-2.9c0.6-0.7,1.4-1.1,2.5-1.4
                  c0.5-0.1,1.2-0.2,1.8-0.1c0.7,0.1,1.6,0.2,2.9,0.4c0.9,0.2,1.7,0.3,2.4,0.5c0.7,0.1,1.2,0.3,1.6,0.5c0.4,0.2,0.8,0.4,1.1,0.6
                  c0.3,0.2,0.5,0.4,0.8,0.7c0.6,0.7,1,1.4,1.1,2.3s0,2-0.2,3.4l-1,5.3c-0.3,1.5-0.6,2.6-1,3.4c-0.4,0.8-0.9,1.8-1.6,2.8l-10.2,13.8
                  l8.7,1.7l-1,5.2l-15.3-2.9L473.7,72.7z"
                />
                <path class="st60" d="M527.9,62.8l-3.6-1.2l1.6-5c1.6,0.3,2.9,0.2,3.8-0.4c1-0.6,1.9-1.6,2.6-3.1l5,1.6l-12.1,37.2L519,90
                  L527.9,62.8z"
                />
                <path class="st60" d="M540.2,80.1l-3.1,8.2c-0.3,0.8-0.4,1.4-0.4,1.7s0.3,0.7,0.7,0.8c0.4,0.1,0.7,0.1,1-0.1s0.5-0.7,0.8-1.4
                  l0.1-0.2l4.9-13c0.3-0.7,0.3-1.1,0.3-1.5s-0.3-0.6-0.7-0.7c-0.7-0.3-1.3,0-1.5,0.8l-0.2,0.5l-0.5,1.3l-6.2-2.3l6.7-17.8l14.6,5.5
                  l-1.9,4.9l-8.4-3.2l-2.4,6.5c1.4-0.7,2.8-0.8,4.2-0.3c1.5,0.6,2.5,1.6,2.9,3c0.4,1.4,0.3,3.1-0.5,5.1l-0.2,0.6l-5.1,13.7l-0.3,0.9
                  c-0.8,2.2-2,3.4-3.5,3.7c-0.7,0.2-1.5,0.2-2.3,0.1s-1.9-0.5-3.4-1.1c-0.7-0.3-1.4-0.5-1.9-0.7c-0.5-0.2-1-0.4-1.3-0.6
                  c-0.4-0.2-0.7-0.4-1-0.6c-0.3-0.2-0.5-0.4-0.7-0.7c-1.2-1.4-1.5-3.1-0.7-5.3l0.3-0.9l3.5-9.3L540.2,80.1z"
                />
                <path class="st60" d="M585.5,86.8l-3.3-1.8l2.5-4.7c1.5,0.6,2.8,0.7,3.8,0.3c1.1-0.4,2.1-1.2,3.1-2.6l4.7,2.5L578,115.1l-5.9-3.1
                  L585.5,86.8z"
                />
                <path class="st60" d="M593.6,108.2l-2.9,4.9l-0.2,0.3c-0.5,0.8-0.7,1.4-0.8,1.8c0,0.4,0.2,0.7,0.5,0.9s0.7,0.2,1,0s0.7-0.7,1.1-1.4
                  l0.2-0.4l4.6-7.8c-1,0.2-1.7,0.3-2.3,0.2c-0.5-0.1-1.1-0.3-1.7-0.6c-1.5-0.9-2.3-2.1-2.6-3.7c-0.1-0.7,0-1.4,0.1-2.1
                  c0.2-0.7,0.5-1.5,1.1-2.4l0.3-0.6l5.2-8.9c0.7-1.3,1.3-2.1,1.8-2.6c0.5-0.5,1.2-0.8,1.9-1c0.4-0.1,0.8-0.2,1.3-0.2
                  c0.4,0,0.8,0.1,1.3,0.2c0.5,0.1,1,0.4,1.6,0.7c0.6,0.3,1.3,0.7,2.2,1.2c1.4,0.8,2.4,1.5,3,1.9c0.6,0.5,1,1,1.3,1.6
                  c0.4,0.9,0.6,1.7,0.5,2.6s-0.5,1.8-1.2,3l-0.4,0.7l-13,22.2l-0.3,0.5c-0.6,1.1-1.2,1.9-1.8,2.4c-0.5,0.5-1.2,0.8-1.9,0.9
                  c-0.9,0.2-1.7,0.1-2.5-0.1s-1.9-0.7-3.2-1.5c-1-0.6-1.7-1-2.3-1.4c-0.6-0.4-1.1-0.8-1.5-1.1s-0.7-0.7-0.9-1
                  c-0.2-0.3-0.4-0.7-0.5-1.1c-0.2-0.8-0.3-1.5-0.1-2.2c0.1-0.7,0.5-1.5,1-2.5l0.2-0.4l3.8-6.4L593.6,108.2z M598.2,100.3l-0.1,0.1
                  c-0.7,1.2-0.7,2,0,2.4c0.4,0.2,0.7,0.2,1,0.1c0.3-0.2,0.7-0.6,1.1-1.3l4.5-7.7c0.4-0.7,0.6-1.2,0.6-1.6c0-0.4-0.2-0.6-0.5-0.9
                  c-0.7-0.4-1.4,0-2,1.2l-0.1,0.1L598.2,100.3z"
                />
                <path class="st60" d="M630.6,139.6l-6.7-5.2l3.1-3.9l20-17.1l7.4,5.8l-15.7,20.1l1.6,1.2l-3.3,4.2l-1.6-1.2l-5.2,6.6l-4.9-3.8
                  L630.6,139.6z M633.9,135.5l9.4-12.1L632,134L633.9,135.5z"
                />
                <path class="st60" d="M664.3,172.8l18.8-6c0.8-0.2,1.2-0.4,1.3-0.5l0.8-0.8l4-3.7l0.3-0.3c0.8-0.8,1-1.5,0.5-2
                  c-0.5-0.6-1.2-0.5-2,0.2l-0.3,0.3l-4.6,4.3l-4.5-4.9l5.2-4.8l0.8-0.7c0.9-0.9,1.8-1.4,2.7-1.6c0.9-0.2,1.8-0.1,2.8,0.3
                  c0.5,0.2,1,0.5,1.5,1c0.5,0.4,1.2,1.1,2.1,2.1c0.6,0.7,1.2,1.3,1.6,1.8s0.8,0.9,1,1.3c0.2,0.4,0.4,0.8,0.5,1.1
                  c0.1,0.3,0.2,0.7,0.2,1.1c0.1,0.9,0,1.7-0.4,2.5c-0.4,0.7-1.1,1.6-2.2,2.6l-4,3.7c-1.1,1-2,1.8-2.8,2.2c-0.8,0.5-1.8,0.9-2.9,1.3
                  l-16.4,5.1l6,6.4l-3.9,3.6L659.9,177L664.3,172.8z"
                />
                <path class="st60" d="M695.5,173.6l-2.4-2.9l4-3.4c1.2,1.1,2.3,1.7,3.5,1.7c1.1,0,2.4-0.4,3.9-1.3l3.4,4L678,197.2l-4.3-5
                  L695.5,173.6z"
                />
                <path class="st60" d="M706.7,225.8l19.6-2.2c0.8-0.1,1.3-0.2,1.4-0.3l1-0.6l4.6-2.8l0.3-0.2c1-0.6,1.2-1.2,0.8-1.9
                  c-0.4-0.7-1.1-0.7-2-0.2l-0.3,0.2l-5.3,3.3l-3.5-5.6l6-3.7l0.9-0.6c1.1-0.7,2-1,3-1s1.8,0.3,2.7,0.9c0.5,0.3,0.9,0.7,1.3,1.3
                  c0.4,0.5,1,1.3,1.6,2.4c0.5,0.8,0.9,1.5,1.2,2.1c0.3,0.6,0.6,1.1,0.7,1.5c0.2,0.4,0.3,0.8,0.3,1.2s0,0.7,0,1.1
                  c-0.1,0.9-0.4,1.7-0.9,2.4c-0.5,0.6-1.4,1.4-2.7,2.1l-4.6,2.8c-1.3,0.8-2.3,1.3-3.2,1.6s-1.9,0.5-3.1,0.7l-17,1.8l4.6,7.5l-4.5,2.8
                  l-8.2-13.3L706.7,225.8z"
                />
                <path class="st60" d="M729.4,266.3l19.7,0.5c0.8,0,1.3,0,1.5-0.1l1-0.5l4.9-2.2l0.4-0.2c1-0.5,1.4-1,1.1-1.8
                  c-0.3-0.7-1-0.9-1.9-0.4l-0.4,0.2l-5.7,2.6l-2.7-6.1l6.4-2.9l1-0.4c1.1-0.5,2.2-0.7,3.1-0.6s1.7,0.5,2.5,1.2
                  c0.4,0.4,0.8,0.8,1.2,1.4c0.3,0.6,0.8,1.5,1.3,2.6c0.4,0.9,0.7,1.6,1,2.2s0.4,1.1,0.5,1.6s0.1,0.9,0.1,1.2s0,0.7-0.1,1.1
                  c-0.2,0.9-0.6,1.6-1.2,2.2c-0.6,0.6-1.6,1.2-2.9,1.8l-4.9,2.2c-1.4,0.6-2.5,1-3.4,1.2s-2,0.3-3.2,0.3l-17.1-0.5l3.6,8l-4.8,2.1
                  l-6.4-14.3L729.4,266.3z"
                />
                <path class="st60" d="M748.8,285.9l-8.2,3.1c-0.8,0.3-1.3,0.6-1.5,0.9c-0.3,0.3-0.3,0.6-0.2,1s0.4,0.6,0.8,0.7
                  c0.3,0,0.9-0.1,1.6-0.3l0.2-0.1l13-5c0.7-0.3,1.1-0.5,1.3-0.8c0.2-0.3,0.2-0.6,0.1-1c-0.3-0.7-0.8-0.9-1.6-0.6l-0.5,0.2l-1.3,0.5
                  l-2.4-6.2l17.8-6.8l5.6,14.6l-4.9,1.9l-3.2-8.4l-6.5,2.5c1.5,0.6,2.4,1.6,3,3c0.6,1.5,0.5,2.9-0.3,4.2c-0.8,1.3-2.2,2.3-4.2,3
                  l-0.6,0.2l-13.7,5.2l-0.9,0.4c-2.2,0.8-3.9,0.8-5.1-0.2c-0.6-0.4-1.1-1-1.5-1.6c-0.4-0.7-0.9-1.8-1.5-3.2c-0.3-0.7-0.5-1.4-0.7-1.9
                  s-0.3-1-0.4-1.4c-0.1-0.4-0.2-0.8-0.2-1.1c0-0.4,0-0.7,0-1c0.2-1.8,1.4-3.2,3.5-4l0.9-0.4l9.3-3.5L748.8,285.9z"
                />
                <path class="st60" d="M773.8,324.2l-0.9-3.6l5.1-1.3c0.6,1.5,1.3,2.5,2.3,3.1c1,0.5,2.3,0.8,4,0.6l1.3,5.1l-38,9.3l-1.6-6.4
                  L773.8,324.2z"
                />
                <path class="st60" d="M783.1,341.3l-1.5-7.6l5.2-1l2.8,14l-6.6,1.3l-33.4-1.6l-1.3-6.6L783.1,341.3z" />
                <path class="st60" d="M771.8,389.3l-8.8,0.6l-0.3,0c-1.3,0.1-1.9,0.5-1.8,1.3c0,0.8,0.7,1.1,2.1,1l0.2,0l8.8-0.6l0.4,0
                  c1.2-0.1,1.7-0.7,1.6-1.8l0-0.4l-0.1-1.7l5.3-0.4l0.1,1.4c0.1,1.1,0.2,1.7,0.5,1.9c0.3,0.2,0.9,0.3,1.8,0.3l0.5,0l5.2-0.4l0.6,0
                  c1.2-0.1,1.7-0.5,1.7-1.3s-0.7-1.1-2-1l-0.5,0l-5.9,0.4l-0.5-6.6l6.2-0.4l1-0.1c1.4-0.1,2.5-0.1,3.3,0.1s1.4,0.7,2.1,1.3
                  c0.6,0.7,1.1,1.8,1.3,3.1c0,0.2,0.1,0.6,0.1,1s0.1,1,0.1,1.8c0.1,0.9,0.1,1.7,0.1,2.4c0,0.7,0,1.2-0.1,1.7
                  c-0.1,0.5-0.2,0.9-0.4,1.2c-0.2,0.4-0.4,0.7-0.6,1c-0.5,0.6-1.1,1.1-1.7,1.3c-0.7,0.2-1.7,0.4-3.1,0.6l-0.8,0l-5.3,0.4l-0.6,0
                  c-1.4,0.1-2.5,0-3.3-0.4s-1.4-1.1-2.1-2.1c-0.5,1.2-1.1,2-1.8,2.4s-1.9,0.7-3.5,0.8l-8.3,0.6l-0.9,0.1c-1.6,0.1-2.8,0-3.6-0.3
                  s-1.5-0.9-2.1-1.7c-0.2-0.3-0.3-0.5-0.5-0.8c-0.1-0.3-0.3-0.6-0.3-1c-0.1-0.4-0.2-0.9-0.2-1.5c-0.1-0.6-0.1-1.3-0.2-2.1
                  c-0.1-1.7-0.1-2.9,0-3.8c0.1-0.8,0.4-1.6,0.8-2.2c0.5-0.7,1.1-1.2,1.8-1.5c0.7-0.3,1.6-0.5,2.8-0.6l0.6,0l9.9-0.7L771.8,389.3z"
                />
                <path class="st60" d="M764.8,409.4l-0.1-8.5l5-0.1l25.9,4.9l0.2,9.4l-25.5,0.4l0,2l-5.3,0.1l0-2l-8.4,0.1l-0.1-6.2L764.8,409.4z
                  M770.1,409.3l15.3-0.3l-15.4-2.1L770.1,409.3z"
                />
                <path class="st60" d="M779.5,458.6l6.4,0.8c1.2,0.1,1.8-0.2,1.9-1s-0.4-1.2-1.4-1.3l-0.4,0l-9.5-1.1c0.5,0.8,0.8,1.4,0.9,2
                  c0.2,0.6,0.2,1.2,0.1,1.9c-0.2,1.6-0.9,2.8-2.2,3.6c-1.3,0.8-3,1-5.1,0.8l-0.5-0.1l-10.1-1.2l-1-0.1c-2.6-0.3-4.1-1.5-4.7-3.6
                  c-0.1-0.3-0.1-0.6-0.2-0.8c0-0.3,0-0.6,0-0.9c0-0.4,0-0.8,0.1-1.3c0-0.5,0.1-1.2,0.2-1.9c0.1-0.9,0.2-1.6,0.3-2.2s0.2-1.1,0.3-1.5
                  c0.1-0.4,0.3-0.8,0.5-1.1s0.4-0.6,0.7-0.8c0.7-0.7,1.4-1.1,2.1-1.3s1.8-0.2,3.1,0l0.8,0.1l24.6,2.9l1.2,0.1c1.8,0.2,3,0.6,3.8,1.1
                  s1.4,1.4,1.7,2.6c0.1,0.3,0.2,0.5,0.2,0.8c0,0.3,0,0.6,0,0.9s0,0.8-0.1,1.3c0,0.5-0.1,1.1-0.2,1.9c-0.2,1.6-0.4,2.8-0.6,3.5
                  c-0.2,0.7-0.5,1.4-0.9,1.9c-0.6,0.7-1.3,1.2-2.2,1.5s-1.9,0.3-3.2,0.2l-0.5-0.1l-7-0.8L779.5,458.6z M770.5,457.6
                  c0.8,0.1,1.3,0.1,1.6-0.1s0.5-0.4,0.5-0.8c0-0.4-0.1-0.8-0.3-1s-0.8-0.4-1.6-0.5l-8.9-1c-0.9-0.1-1.6-0.1-2,0
                  c-0.4,0.1-0.6,0.4-0.6,0.8c0,0.5,0.1,0.8,0.4,1c0.3,0.2,0.9,0.3,1.8,0.5l0.4,0L770.5,457.6z"
                />
                <path class="st60" d="M753.7,493.5l15.2,12.6c0.6,0.5,1,0.8,1.2,0.8l1.1,0.3l5.2,1.3l0.4,0.1c1.1,0.3,1.8,0,1.9-0.7
                  c0.2-0.8-0.2-1.3-1.2-1.5l-0.4-0.1l-6.1-1.6l1.7-6.4l6.8,1.8l1,0.3c1.2,0.3,2.1,0.8,2.8,1.4c0.7,0.6,1.1,1.5,1.2,2.5
                  c0.1,0.5,0.1,1.2,0,1.8c-0.1,0.7-0.3,1.6-0.6,2.9c-0.2,0.9-0.4,1.7-0.6,2.3c-0.2,0.6-0.4,1.2-0.6,1.6c-0.2,0.4-0.4,0.8-0.6,1
                  s-0.5,0.5-0.8,0.8c-0.7,0.6-1.5,0.9-2.3,1c-0.8,0.1-2-0.1-3.4-0.5l-5.2-1.3c-1.4-0.4-2.6-0.8-3.4-1.2c-0.8-0.4-1.7-1-2.7-1.8
                  l-13.1-11.1l-2.2,8.5l-5.1-1.3l3.9-15.1L753.7,493.5z"
                />
                <path class="st60" d="M773.2,527.1l2.3-7.4l5,1.6l-4.3,13.6l-6.4-2l-28.5-17.5l2-6.4L773.2,527.1z" />
                <path class="st60" d="M751.4,567.6l1.5-3.4l4.8,2.2c-0.5,1.5-0.5,2.8-0.1,3.9c0.4,1,1.4,2,2.8,3l-2.2,4.8l-35.7-16.1l2.7-6
                  L751.4,567.6z"
                />
                <path class="st60" d="M732.8,578l-7.9-4l-0.3-0.1c-1.2-0.6-1.9-0.5-2.3,0.2c-0.3,0.7,0.1,1.3,1.3,2l0.2,0.1l7.9,4l0.4,0.2
                  c1,0.5,1.8,0.3,2.3-0.8l0.2-0.3l0.8-1.5l4.7,2.4l-0.6,1.3c-0.5,0.9-0.7,1.6-0.6,1.9c0.1,0.3,0.6,0.7,1.5,1.2l0.5,0.2l4.7,2.4
                  l0.5,0.3c1,0.5,1.8,0.4,2.1-0.3c0.3-0.7,0-1.3-1.2-1.9l-0.4-0.2l-5.2-2.7l3-5.9l5.5,2.8l0.9,0.5c1.3,0.6,2.2,1.2,2.8,1.8
                  c0.5,0.6,0.9,1.3,1.1,2.2c0.2,1,0,2.1-0.5,3.3c-0.1,0.2-0.2,0.6-0.4,1c-0.2,0.4-0.5,0.9-0.8,1.6c-0.4,0.8-0.8,1.5-1.1,2.1
                  c-0.3,0.6-0.7,1-1,1.4s-0.6,0.7-0.9,0.9c-0.3,0.2-0.7,0.4-1.1,0.6c-0.8,0.3-1.5,0.3-2.2,0.2c-0.7-0.1-1.7-0.5-2.9-1.1l-0.7-0.3
                  l-4.7-2.4l-0.5-0.3c-1.3-0.7-2.2-1.3-2.6-2c-0.5-0.7-0.7-1.7-0.7-2.9c-1,0.8-2,1.1-2.8,1.1s-2-0.3-3.4-1.1l-7.4-3.8l-0.8-0.4
                  c-1.4-0.8-2.4-1.5-2.9-2.1s-0.9-1.5-0.9-2.5c0-0.3,0-0.6,0-1c0-0.3,0.1-0.7,0.2-1.1s0.3-0.9,0.6-1.4c0.3-0.5,0.6-1.2,0.9-1.9
                  c0.8-1.5,1.4-2.6,1.9-3.2c0.5-0.7,1.1-1.1,1.8-1.5c0.8-0.3,1.6-0.5,2.3-0.4c0.8,0.1,1.7,0.4,2.7,0.9l0.5,0.3l8.8,4.5L732.8,578z"
                />
                <path class="st60" d="M710.5,616.2l-7.3-5L703,611c-1.1-0.7-1.8-0.8-2.3-0.1c-0.4,0.6-0.1,1.3,1,2.1l0.2,0.1l7.3,5l0.3,0.2
                  c1,0.7,1.8,0.5,2.4-0.4l0.2-0.3l1-1.4l4.3,3l-0.8,1.2c-0.6,0.9-0.9,1.5-0.8,1.8c0,0.3,0.5,0.8,1.3,1.3l0.4,0.3l4.3,3l0.5,0.3
                  c1,0.7,1.7,0.7,2.1,0c0.4-0.7,0.1-1.3-0.9-2.1l-0.4-0.3l-4.8-3.3l3.8-5.5l5.1,3.5l0.8,0.6c1.2,0.8,2,1.5,2.5,2.1s0.7,1.4,0.8,2.3
                  c0,1-0.3,2.1-0.9,3.2c-0.1,0.2-0.3,0.5-0.5,0.9s-0.6,0.9-1,1.4c-0.5,0.8-1,1.4-1.4,2s-0.8,0.9-1.1,1.3s-0.7,0.6-1,0.7
                  c-0.3,0.2-0.7,0.3-1.1,0.4c-0.8,0.2-1.5,0.2-2.2-0.1c-0.7-0.2-1.6-0.7-2.8-1.5l-0.6-0.4l-4.3-3l-0.5-0.3c-1.2-0.8-2-1.6-2.3-2.3
                  c-0.4-0.7-0.5-1.7-0.3-3c-1.1,0.6-2.1,0.8-2.9,0.8s-1.9-0.6-3.2-1.5l-6.9-4.7l-0.8-0.5c-1.3-0.9-2.2-1.8-2.6-2.5
                  c-0.5-0.7-0.7-1.6-0.6-2.7c0-0.3,0.1-0.6,0.1-0.9c0.1-0.3,0.2-0.7,0.4-1s0.4-0.8,0.8-1.3c0.3-0.5,0.7-1.1,1.2-1.8
                  c1-1.4,1.7-2.4,2.3-2.9c0.6-0.6,1.3-1,2-1.2c0.8-0.2,1.6-0.3,2.3-0.1c0.7,0.2,1.6,0.6,2.5,1.3l0.5,0.3l8.2,5.6L710.5,616.2z"
                />
                <path class="st60" d="M705.2,639.3l5.1,3.9c1,0.7,1.7,0.8,2.2,0.2s0.3-1.2-0.5-1.9l-0.3-0.3l-7.5-5.8c0,0.9-0.1,1.6-0.3,2.2
                  c-0.2,0.6-0.5,1.2-0.9,1.7c-1,1.3-2.3,1.9-3.8,1.9s-3.1-0.7-4.8-1.9l-0.4-0.3l-8-6.2l-0.8-0.7c-2-1.6-2.7-3.4-2.1-5.5
                  c0.1-0.3,0.2-0.6,0.3-0.8c0.1-0.2,0.3-0.5,0.4-0.8c0.2-0.3,0.4-0.7,0.7-1.1c0.3-0.4,0.7-0.9,1.1-1.5c0.5-0.7,1-1.3,1.4-1.8
                  c0.4-0.5,0.8-0.8,1.1-1.1c0.3-0.3,0.7-0.5,1-0.7s0.7-0.3,1-0.3c0.9-0.2,1.7-0.2,2.5,0s1.6,0.8,2.7,1.6l0.6,0.5l19.6,15.1l0.9,0.7
                  c1.4,1.1,2.3,2.1,2.7,2.9c0.4,0.9,0.5,1.9,0.1,3.1c0,0.3-0.1,0.5-0.2,0.7s-0.3,0.5-0.5,0.8c-0.2,0.3-0.5,0.6-0.7,1.1
                  c-0.3,0.4-0.7,0.9-1.1,1.5c-1,1.3-1.8,2.2-2.3,2.7s-1.1,0.9-1.8,1.2c-0.9,0.3-1.8,0.4-2.6,0.2s-1.8-0.7-2.8-1.5l-0.4-0.3l-5.6-4.3
                  L705.2,639.3z M698,633.8c0.6,0.5,1.1,0.7,1.4,0.8s0.6-0.1,0.9-0.5c0.3-0.3,0.3-0.7,0.2-1c-0.1-0.3-0.5-0.7-1.1-1.2l-7.1-5.5
                  c-0.7-0.6-1.3-0.9-1.7-1c-0.4-0.1-0.7,0.1-1,0.4c-0.3,0.4-0.3,0.7-0.2,1.1c0.2,0.3,0.6,0.8,1.3,1.3l0.3,0.3L698,633.8z"
                />
                <path class="st60" d="M679.5,673.3l2.7-2.7l3.8,3.7c-1,1.3-1.5,2.5-1.4,3.6c0,1.1,0.6,2.4,1.6,3.8l-3.7,3.8l-27.8-27.6l4.7-4.7
                  L679.5,673.3z"
                />
                <path class="st60" d="M670.2,682.4l2.7-2.6l3.6,3.8c-1,1.2-1.5,2.4-1.5,3.5s0.5,2.4,1.4,3.8l-3.8,3.6l-26.8-28.5l4.8-4.5
                  L670.2,682.4z"
                />
                <path class="st60" d="M626.7,702.8l-5.2-7.2l-0.2-0.2c-0.8-1-1.5-1.3-2.1-0.9c-0.6,0.5-0.6,1.2,0.2,2.3l0.1,0.2l5.2,7.2l0.2,0.3
                  c0.7,0.9,1.5,1.1,2.4,0.4l0.3-0.2l1.4-1l3.1,4.3l-1.1,0.8c-0.8,0.6-1.3,1.1-1.4,1.4c-0.1,0.3,0.2,0.9,0.8,1.7l0.3,0.4l3.1,4.2
                  l0.3,0.5c0.7,1,1.3,1.2,2,0.7c0.6-0.5,0.6-1.2-0.2-2.3l-0.3-0.4l-3.5-4.7l5.4-3.9l3.7,5l0.6,0.8c0.9,1.2,1.4,2.1,1.6,2.8
                  c0.2,0.7,0.2,1.6,0,2.4c-0.3,0.9-0.9,1.8-1.9,2.7c-0.2,0.2-0.4,0.4-0.8,0.7s-0.8,0.6-1.4,1c-0.8,0.6-1.4,1-2,1.4s-1.1,0.6-1.5,0.8
                  c-0.4,0.2-0.8,0.3-1.2,0.3c-0.4,0-0.8,0-1.2,0c-0.8-0.1-1.5-0.4-2-0.8c-0.5-0.4-1.3-1.2-2.1-2.3l-0.5-0.6l-3.1-4.3l-0.3-0.5
                  c-0.8-1.2-1.3-2.2-1.4-3c-0.1-0.8,0.1-1.8,0.7-2.9c-1.3,0.2-2.3,0.1-3-0.3c-0.8-0.4-1.6-1.2-2.5-2.5l-4.9-6.7l-0.5-0.8
                  c-0.9-1.3-1.5-2.4-1.7-3.2s-0.1-1.7,0.3-2.7c0.1-0.3,0.3-0.6,0.5-0.8c0.2-0.3,0.4-0.5,0.7-0.8s0.7-0.6,1.1-1c0.5-0.4,1-0.8,1.7-1.3
                  c1.4-1,2.4-1.7,3.2-2c0.8-0.3,1.5-0.5,2.2-0.5c0.9,0,1.6,0.3,2.3,0.7c0.6,0.4,1.3,1.1,2,2l0.3,0.5l5.8,8L626.7,702.8z"
                />
                <path class="st60" d="M625,723.4l0.8,1.2c0.7,1.1,1.1,2,1.2,2.8c0.1,0.8-0.1,1.7-0.5,2.5c-0.1,0.3-0.3,0.5-0.5,0.8
                  c-0.2,0.3-0.5,0.5-0.8,0.8s-0.7,0.6-1.2,0.9c-0.5,0.3-1,0.7-1.7,1.1c-0.8,0.5-1.5,1-2.1,1.3c-0.6,0.3-1.1,0.6-1.5,0.7
                  c-0.5,0.2-0.9,0.3-1.3,0.3c-0.4,0-0.8,0-1.2,0c-1.1-0.2-2-0.8-2.9-1.8c-0.2-0.2-0.4-0.5-0.5-0.7c-0.1-0.2-0.4-0.7-0.9-1.4l-13.6-21
                  c-0.4-0.6-0.7-1-0.9-1.4s-0.4-0.7-0.5-0.9c-0.1-0.3-0.2-0.5-0.3-0.7c-0.1-0.2-0.1-0.4-0.2-0.6c-0.1-0.7-0.1-1.4,0.1-2.1
                  c0.2-0.7,0.6-1.4,1.1-1.9c0.3-0.3,0.7-0.7,1.2-1.1c0.5-0.4,1.2-0.9,2.2-1.5c0.7-0.4,1.2-0.8,1.7-1.1c0.5-0.3,0.9-0.5,1.3-0.7
                  c0.4-0.2,0.7-0.3,1-0.4c0.3-0.1,0.6-0.1,1-0.1c1,0,1.8,0.1,2.5,0.6c0.7,0.4,1.4,1.2,2.1,2.2l0.8,1.2L625,723.4z M616.9,727.5
                  l0.5,0.7c0.4,0.6,0.8,1,1.1,1.2s0.6,0.1,1-0.1c0.5-0.3,0.6-0.8,0.3-1.4c0-0.2-0.1-0.3-0.3-0.6c-0.1-0.2-0.3-0.6-0.6-1l-12.5-19.3
                  c-0.4-0.6-0.6-0.9-0.8-1.1c-0.1-0.2-0.3-0.3-0.4-0.4c-0.5-0.4-1-0.5-1.4-0.2c-0.7,0.4-0.6,1.2,0.1,2.4l0.5,0.7L616.9,727.5z"
                />
                <path class="st60" d="M568.4,743.2c0.6,0,1.2,0.1,1.6,0.2c0.5,0.1,0.9,0.3,1.2,0.6c0.3,0.3,0.7,0.6,1,1.1c0.3,0.4,0.6,1,0.9,1.7
                  l0.2,0.5l2.2,5.1l0.5,1.1c0.5,1.3,0.8,2.3,0.7,3.2c-0.1,0.9-0.4,1.8-1.1,2.6c-0.4,0.5-0.8,0.9-1.5,1.2c-0.6,0.4-1.7,0.9-3.2,1.5
                  c-1.5,0.7-2.7,1.1-3.5,1.3c-0.8,0.2-1.5,0.2-2.2,0c-0.9-0.2-1.6-0.6-2.1-1.2c-0.6-0.6-1.1-1.5-1.7-2.8l-0.2-0.4l-2.4-5.4l-0.2-0.5
                  c-0.5-1.2-0.7-2.2-0.6-3.1c0.1-0.9,0.5-1.8,1.2-2.7c-1.3-0.1-2.2-0.4-2.9-0.9c-0.7-0.5-1.3-1.4-1.9-2.8l-0.3-0.8l-3.3-7.5l-0.3-0.8
                  c-0.5-1.2-0.8-2.1-0.7-2.9c0-0.8,0.3-1.6,0.8-2.4c0.2-0.3,0.4-0.6,0.6-0.8s0.5-0.5,0.9-0.7s0.8-0.5,1.4-0.8c0.6-0.3,1.3-0.6,2.1-1
                  c1.7-0.7,3-1.2,3.8-1.3s1.8,0,2.6,0.3c0.7,0.3,1.3,0.7,1.7,1.2c0.4,0.6,0.9,1.5,1.5,2.9l3.1,7l0.5,1.1c0.3,0.7,0.5,1.3,0.7,1.9
                  c0.2,0.5,0.2,1,0.2,1.5c0,0.5-0.2,0.9-0.4,1.3C569.1,742.2,568.8,742.6,568.4,743.2z M560.8,741l0.1,0.3c0.5,1.3,1.2,1.7,1.9,1.4
                  c0.7-0.3,0.8-1.1,0.3-2.3l-0.1-0.3l-3.4-7.7l-0.1-0.3c-0.6-1.2-1.2-1.7-1.9-1.4c-0.7,0.3-0.8,1.1-0.3,2.3l0.1,0.2L560.8,741z
                  M567.2,755.7l0.1,0.3c0.4,1,1,1.3,1.7,1c0.7-0.3,0.8-1,0.4-2l-0.1-0.3l-2.5-5.8l-0.1-0.3c-0.5-1-1.1-1.4-1.8-1.1
                  c-0.8,0.3-0.9,1-0.4,2l0.1,0.3L567.2,755.7z"
                />
                <path class="st60" d="M518.5,746.2l-3.3,19.5c-0.2,0.8-0.2,1.3-0.1,1.4l0.3,1.1l1.5,5.2l0.1,0.4c0.3,1.1,0.8,1.5,1.6,1.3
                  c0.8-0.2,1-0.8,0.7-1.8l-0.1-0.4l-1.7-6l6.4-1.8l1.9,6.8l0.3,1c0.3,1.2,0.4,2.2,0.2,3.1c-0.2,0.9-0.7,1.7-1.6,2.3
                  c-0.4,0.4-1,0.7-1.6,0.9c-0.6,0.3-1.5,0.6-2.8,0.9c-0.9,0.3-1.7,0.5-2.3,0.6c-0.6,0.2-1.2,0.3-1.7,0.3c-0.5,0-0.9,0-1.2,0
                  c-0.3,0-0.7-0.1-1.1-0.3c-0.9-0.3-1.5-0.8-2-1.5c-0.5-0.7-0.9-1.7-1.3-3.2l-1.5-5.2c-0.4-1.4-0.6-2.6-0.7-3.5s0-2,0.2-3.2l3-16.9
                  l-8.5,2.4l-1.5-5.1l15-4.3L518.5,746.2z"
                />
                <path class="st60" d="M496.4,762.6l-2-8.6l-0.1-0.3c-0.3-1.3-0.8-1.8-1.6-1.6c-0.8,0.2-1,0.9-0.7,2.2l0,0.2l2,8.6l0.1,0.4
                  c0.3,1.1,0.9,1.6,2,1.3l0.3-0.1l1.7-0.4l1.2,5.2l-1.4,0.3c-1,0.2-1.6,0.5-1.8,0.8s-0.2,0.9,0,1.9l0.1,0.5l1.2,5.1l0.1,0.6
                  c0.3,1.2,0.8,1.6,1.5,1.5c0.8-0.2,1-0.9,0.7-2.2l-0.1-0.5l-1.3-5.7l6.5-1.5l1.4,6.1l0.2,1c0.4,1.4,0.5,2.5,0.4,3.3
                  c-0.1,0.8-0.4,1.5-1,2.2c-0.6,0.8-1.6,1.3-2.9,1.7c-0.2,0.1-0.6,0.2-1,0.3c-0.5,0.1-1,0.2-1.7,0.4c-0.9,0.2-1.7,0.4-2.4,0.5
                  s-1.2,0.2-1.7,0.2c-0.5,0-0.9,0-1.3-0.2c-0.4-0.1-0.7-0.3-1.1-0.5c-0.7-0.4-1.2-0.9-1.5-1.5c-0.3-0.6-0.7-1.6-1-3l-0.2-0.7
                  l-1.2-5.2l-0.1-0.6c-0.3-1.4-0.4-2.5-0.2-3.3c0.2-0.8,0.8-1.6,1.8-2.4c-1.2-0.3-2.1-0.8-2.7-1.4c-0.5-0.6-1-1.7-1.3-3.3l-1.8-8.1
                  l-0.2-0.9c-0.3-1.6-0.4-2.8-0.3-3.6c0.2-0.8,0.6-1.6,1.4-2.4c0.2-0.2,0.5-0.4,0.7-0.6c0.3-0.2,0.6-0.3,1-0.5
                  c0.4-0.2,0.9-0.3,1.4-0.4c0.6-0.2,1.3-0.3,2-0.5c1.6-0.4,2.9-0.6,3.7-0.6c0.8,0,1.6,0.1,2.3,0.4c0.8,0.4,1.4,0.9,1.8,1.5
                  s0.8,1.5,1,2.7l0.1,0.6l2.2,9.7L496.4,762.6z"
                />
                <path class="st60" d="M457,783l3.7-0.4l0.5,5.3c-1.6,0.3-2.7,1-3.4,1.9c-0.7,0.9-1.1,2.2-1.2,3.9l-5.3,0.5l-3.9-39l6.6-0.7L457,783
                  z"
                />
                <path class="st60" d="M446,787.5l0.1,1.5c0.1,1.3-0.1,2.3-0.4,3c-0.3,0.8-0.9,1.4-1.7,1.9c-0.3,0.2-0.5,0.3-0.9,0.5
                  s-0.7,0.2-1.1,0.3c-0.4,0.1-0.9,0.2-1.5,0.2c-0.6,0.1-1.2,0.1-2,0.1c-1,0.1-1.8,0.1-2.5,0.1c-0.7,0-1.2-0.1-1.7-0.1
                  c-0.5-0.1-0.9-0.2-1.3-0.4c-0.4-0.2-0.7-0.4-1-0.7c-0.8-0.7-1.3-1.8-1.6-3c0-0.3-0.1-0.6-0.1-0.9c0-0.2,0-0.8-0.1-1.7l-1.4-25
                  c0-0.7-0.1-1.2-0.1-1.6c0-0.4,0-0.8,0-1.1c0-0.3,0-0.5,0.1-0.7c0-0.2,0.1-0.4,0.2-0.6c0.2-0.7,0.6-1.3,1.2-1.8s1.2-0.9,1.9-1.1
                  c0.4-0.1,1-0.3,1.6-0.3s1.5-0.2,2.6-0.2c0.8,0,1.5-0.1,2-0.1c0.6,0,1.1,0,1.5,0.1c0.4,0.1,0.8,0.1,1.1,0.2c0.3,0.1,0.6,0.2,0.9,0.4
                  c0.8,0.4,1.5,1,1.9,1.8c0.4,0.7,0.7,1.7,0.7,3l0.1,1.4L446,787.5z M437,787.1l0,0.8c0,0.7,0.2,1.3,0.3,1.5c0.2,0.3,0.5,0.4,0.9,0.4
                  c0.6,0,0.9-0.4,1-1c0-0.2,0-0.4,0-0.6c0-0.3,0-0.7,0-1.2l-1.3-23c0-0.7-0.1-1.1-0.1-1.3c0-0.2-0.1-0.4-0.1-0.6
                  c-0.2-0.6-0.6-0.9-1.1-0.9c-0.8,0-1.1,0.8-1.1,2.1l0,0.8L437,787.1z"
                />
                <path class="st60" d="M384,771.9l0.9-8.8c0.1-0.8,0.1-1.4-0.1-1.8s-0.4-0.6-0.8-0.6c-0.4-0.1-0.8,0.1-1,0.4
                  c-0.2,0.3-0.3,0.8-0.4,1.6l0,0.2l-1.5,13.8c-0.1,0.7,0,1.2,0.1,1.5c0.1,0.3,0.4,0.5,0.9,0.5c0.8,0.1,1.2-0.3,1.3-1.2l0.1-0.5
                  l0.1-1.3l6.6,0.7l-2,18.9l-15.5-1.6l0.6-5.3l8.9,0.9l0.7-6.9c-1.2,1-2.5,1.4-4,1.3c-1.6-0.2-2.8-0.9-3.6-2.2c-0.8-1.3-1-3-0.8-5.1
                  l0.1-0.6l1.5-14.5l0.1-1c0.2-2.3,1.1-3.8,2.5-4.5c0.7-0.4,1.4-0.6,2.2-0.6c0.8-0.1,2,0,3.5,0.2c0.8,0.1,1.5,0.2,2,0.2
                  c0.6,0.1,1,0.2,1.5,0.3c0.4,0.1,0.8,0.2,1.1,0.3s0.6,0.3,0.9,0.5c1.5,1,2.2,2.7,2,4.9l-0.1,1l-1,9.9L384,771.9z"
                />
                <path class="st60" d="M341.8,754.5l-12.5,15.3c-0.5,0.6-0.8,1-0.8,1.2l-0.3,1.1l-1.3,5.3l-0.1,0.4c-0.3,1.1,0,1.8,0.7,1.9
                  c0.8,0.2,1.3-0.2,1.5-1.3l0.1-0.4l1.5-6.1l6.4,1.6l-1.7,6.8l-0.3,1c-0.3,1.2-0.8,2.2-1.4,2.8c-0.6,0.7-1.5,1.1-2.5,1.3
                  c-0.5,0.1-1.2,0.1-1.8,0c-0.7-0.1-1.6-0.3-2.9-0.6c-0.9-0.2-1.7-0.4-2.3-0.6c-0.6-0.2-1.2-0.4-1.6-0.6c-0.4-0.2-0.8-0.4-1-0.6
                  c-0.3-0.2-0.5-0.5-0.8-0.8c-0.6-0.7-0.9-1.5-1-2.3c-0.1-0.8,0.1-2,0.4-3.4l1.3-5.3c0.3-1.4,0.7-2.6,1.1-3.4c0.4-0.8,1-1.7,1.8-2.7
                  l10.9-13.2l-8.5-2.1l1.3-5.1l15.2,3.7L341.8,754.5z"
                />
                <path class="st60" d="M315.8,750l8.1,2.4l-1.4,4.8l-12.5,23.2l-9-2.7l7.3-24.4l-1.9-0.6l1.5-5.1l1.9,0.6l2.4-8l6,1.8L315.8,750z
                  M314.3,755.1l-4.4,14.7l6.7-14L314.3,755.1z"
                />
                <path class="st60" d="M271.9,755.5l3.4,1.5l-2.1,4.8c-1.5-0.5-2.8-0.5-3.8,0c-1,0.4-2,1.4-2.9,2.8l-4.8-2.1l15.7-35.5l6,2.7
                  L271.9,755.5z"
                />
                <path class="st60" d="M254.8,744.4l-2.9,5.8c-0.5,1.1-0.5,1.8,0.2,2.2c0.7,0.3,1.3,0.1,1.7-0.8l0.2-0.4l4.2-8.5
                  c-0.9,0.2-1.6,0.2-2.2,0.2c-0.6-0.1-1.2-0.3-1.8-0.6c-1.5-0.7-2.4-1.8-2.6-3.3c-0.3-1.5,0-3.2,1-5.1l0.2-0.5l4.5-9.1l0.5-1
                  c1.2-2.3,2.8-3.3,5-3.2c0.3,0,0.6,0,0.8,0.1c0.3,0.1,0.5,0.2,0.9,0.3s0.7,0.3,1.2,0.5c0.5,0.2,1.1,0.5,1.7,0.8
                  c0.8,0.4,1.5,0.8,2,1.1c0.5,0.3,1,0.6,1.3,0.8c0.4,0.3,0.6,0.5,0.8,0.8c0.2,0.3,0.4,0.6,0.5,0.9c0.4,0.9,0.6,1.7,0.5,2.4
                  c-0.1,0.7-0.4,1.7-1,3l-0.3,0.7l-11,22.2l-0.5,1.1c-0.8,1.6-1.6,2.7-2.4,3.2s-1.8,0.8-3,0.7c-0.3,0-0.5,0-0.8-0.1
                  c-0.3-0.1-0.5-0.2-0.9-0.3s-0.7-0.3-1.2-0.5c-0.5-0.2-1-0.5-1.7-0.8c-1.5-0.7-2.5-1.3-3.1-1.8c-0.6-0.4-1.1-0.9-1.5-1.5
                  c-0.5-0.8-0.7-1.7-0.6-2.5c0-0.9,0.4-1.9,0.9-3l0.2-0.5l3.1-6.3L254.8,744.4z M258.9,736.3c-0.4,0.7-0.5,1.2-0.5,1.6
                  c0,0.3,0.2,0.6,0.6,0.8c0.4,0.2,0.7,0.2,1,0c0.3-0.2,0.6-0.6,1-1.3l4-8c0.4-0.8,0.6-1.5,0.6-1.8s-0.2-0.7-0.6-0.9
                  c-0.4-0.2-0.8-0.2-1.1,0.1c-0.3,0.3-0.6,0.8-1,1.6l-0.2,0.4L258.9,736.3z"
                />
                <path class="st60" d="M221.5,713.9l5-7.3l0.2-0.2c0.7-1.1,0.8-1.8,0.1-2.3c-0.6-0.4-1.3-0.1-2.1,1l-0.1,0.2l-5,7.3l-0.2,0.3
                  c-0.7,1-0.5,1.8,0.5,2.4l0.3,0.2l1.4,1l-3,4.4l-1.2-0.8c-0.9-0.6-1.5-0.9-1.8-0.8c-0.3,0.1-0.8,0.5-1.3,1.3l-0.3,0.4l-2.9,4.3
                  l-0.3,0.5c-0.7,1-0.7,1.7,0,2.1c0.6,0.4,1.3,0.1,2.1-1l0.3-0.4l3.3-4.9l5.5,3.7l-3.5,5.2l-0.6,0.8c-0.8,1.2-1.5,2-2.1,2.5
                  c-0.6,0.4-1.4,0.7-2.3,0.8c-1,0.1-2-0.3-3.2-0.9c-0.2-0.1-0.5-0.3-0.9-0.5s-0.9-0.6-1.5-1c-0.8-0.5-1.4-1-2-1.4
                  c-0.5-0.4-1-0.8-1.3-1.1c-0.3-0.3-0.6-0.7-0.8-1c-0.2-0.3-0.3-0.7-0.4-1.1c-0.2-0.8-0.2-1.5,0-2.2c0.2-0.7,0.7-1.6,1.5-2.8l0.4-0.6
                  l3-4.4l0.3-0.5c0.8-1.2,1.6-2,2.3-2.4c0.7-0.4,1.7-0.5,3-0.3c-0.6-1.1-0.9-2.1-0.8-2.9c0.1-0.8,0.6-1.9,1.5-3.2l4.7-6.9l0.5-0.8
                  c0.9-1.3,1.8-2.2,2.5-2.7c0.7-0.5,1.6-0.7,2.6-0.6c0.3,0,0.6,0.1,0.9,0.1c0.3,0.1,0.6,0.2,1,0.4c0.4,0.2,0.8,0.4,1.3,0.7
                  c0.5,0.3,1.1,0.7,1.8,1.2c1.4,0.9,2.4,1.7,3,2.3c0.6,0.6,1,1.3,1.2,1.9c0.2,0.8,0.3,1.6,0.1,2.3s-0.6,1.6-1.2,2.6l-0.3,0.5
                  l-5.6,8.2L221.5,713.9z"
                />
                <path class="st60" d="M206,702.8l5.4-7.1l0.2-0.2c0.8-1,0.9-1.8,0.2-2.3s-1.3-0.2-2.1,0.9l-0.1,0.2l-5.4,7.1l-0.3,0.3
                  c-0.7,0.9-0.6,1.7,0.3,2.4l0.3,0.2l1.4,1l-3.2,4.2l-1.1-0.8c-0.8-0.6-1.4-0.9-1.8-0.9c-0.3,0-0.8,0.4-1.4,1.2l-0.3,0.4l-3.2,4.2
                  l-0.4,0.5c-0.7,0.9-0.8,1.6-0.1,2.1s1.3,0.2,2.1-0.8l0.3-0.4l3.5-4.7l5.3,4l-3.8,5l-0.6,0.8c-0.8,1.2-1.6,2-2.3,2.4
                  c-0.7,0.4-1.4,0.6-2.4,0.7c-1,0-2-0.4-3.2-1.1c-0.2-0.1-0.5-0.3-0.9-0.6c-0.4-0.3-0.8-0.6-1.4-1.1c-0.8-0.6-1.4-1.1-1.9-1.5
                  c-0.5-0.4-0.9-0.8-1.2-1.2s-0.5-0.7-0.7-1.1c-0.1-0.4-0.3-0.7-0.4-1.2c-0.1-0.8-0.1-1.5,0.2-2.2s0.8-1.5,1.6-2.7l0.5-0.6l3.2-4.2
                  l0.4-0.5c0.9-1.2,1.7-1.9,2.4-2.2s1.7-0.4,3-0.1c-0.5-1.2-0.8-2.2-0.6-3c0.1-0.8,0.7-1.9,1.7-3.2l5-6.6l0.6-0.8
                  c1-1.3,1.9-2.1,2.6-2.5c0.7-0.4,1.6-0.6,2.7-0.5c0.3,0,0.6,0.1,0.9,0.2c0.3,0.1,0.6,0.2,1,0.4c0.4,0.2,0.8,0.5,1.3,0.8
                  c0.5,0.3,1,0.8,1.7,1.3c1.3,1,2.3,1.8,2.8,2.5c0.5,0.6,0.9,1.3,1.1,2c0.2,0.8,0.2,1.6,0,2.3c-0.2,0.7-0.7,1.6-1.4,2.5l-0.4,0.5
                  l-6,7.9L206,702.8z"
                />
                <path class="st60" d="M158.4,682.6l2.6,2.7l-3.8,3.7c-1.3-1-2.5-1.5-3.6-1.5s-2.4,0.5-3.8,1.5l-3.7-3.8l28.1-27.3l4.6,4.8
                  L158.4,682.6z"
                />
                <path class="st60" d="M145.4,633.6l-19.3,4c-0.8,0.1-1.2,0.3-1.4,0.4l-0.9,0.7l-4.3,3.3l-0.3,0.3c-0.9,0.7-1.1,1.3-0.6,2
                  c0.5,0.6,1.1,0.6,2,0l0.3-0.3l5-3.8l4,5.3l-5.6,4.2l-0.8,0.6c-1,0.8-2,1.2-2.8,1.3c-0.9,0.1-1.8-0.1-2.7-0.6
                  c-0.5-0.3-1-0.6-1.4-1.1c-0.5-0.5-1.1-1.2-1.9-2.3c-0.6-0.8-1-1.4-1.4-1.9c-0.4-0.5-0.7-1-0.9-1.4c-0.2-0.4-0.3-0.8-0.4-1.1
                  c-0.1-0.3-0.1-0.7-0.1-1.1c0-0.9,0.2-1.7,0.7-2.4c0.5-0.7,1.3-1.5,2.5-2.4l4.3-3.3c1.2-0.9,2.2-1.5,3-1.9c0.8-0.4,1.8-0.7,3-1
                  l16.8-3.3l-5.3-7l4.2-3.2l9.4,12.5L145.4,633.6z"
                />
                <path class="st60" d="M112.7,633.1l-1.2,0.8c-1,0.7-2,1.1-2.8,1.2s-1.7,0-2.5-0.5c-0.3-0.1-0.6-0.3-0.8-0.5
                  c-0.3-0.2-0.5-0.4-0.8-0.8c-0.3-0.3-0.6-0.7-0.9-1.2c-0.3-0.5-0.7-1-1.1-1.7c-0.5-0.8-1-1.5-1.3-2.1c-0.3-0.6-0.6-1.1-0.8-1.5
                  c-0.2-0.5-0.3-0.9-0.3-1.3c0-0.4,0-0.8,0-1.2c0.2-1.1,0.8-2.1,1.8-2.9c0.2-0.2,0.5-0.4,0.7-0.5c0.2-0.1,0.7-0.5,1.4-1l20.8-14
                  c0.5-0.4,1-0.7,1.4-0.9s0.7-0.4,0.9-0.6c0.3-0.2,0.5-0.3,0.7-0.3c0.2-0.1,0.4-0.1,0.6-0.2c0.7-0.1,1.4-0.1,2.1,0.1
                  c0.7,0.2,1.4,0.5,2,1.1c0.3,0.3,0.7,0.7,1.1,1.2c0.4,0.5,0.9,1.2,1.5,2.1c0.4,0.7,0.8,1.2,1.1,1.7s0.5,0.9,0.7,1.3
                  c0.2,0.4,0.3,0.7,0.4,1c0.1,0.3,0.1,0.6,0.1,1c0.1,0.9-0.1,1.8-0.5,2.5s-1.2,1.5-2.2,2.2l-1.2,0.8L112.7,633.1z M108.4,625.1
                  l-0.7,0.5c-0.6,0.4-1,0.8-1.1,1.1c-0.1,0.3-0.1,0.6,0.1,1c0.3,0.5,0.8,0.6,1.4,0.3c0.1,0,0.3-0.1,0.6-0.3s0.6-0.3,1-0.7l19.1-12.9
                  c0.5-0.4,0.9-0.6,1.1-0.8s0.3-0.3,0.4-0.4c0.4-0.5,0.5-1,0.2-1.4c-0.4-0.7-1.2-0.6-2.4,0.2l-0.7,0.5L108.4,625.1z"
                />
                <path class="st60" d="M91.5,591.6l1.8,3.3l-4.6,2.5c-0.9-1.3-1.9-2.1-3-2.4c-1.1-0.3-2.4-0.2-4,0.4l-2.5-4.7l34.5-18.5l3.1,5.8
                  L91.5,591.6z"
                />
                <path class="st60" d="M101.2,566.3l3.7,7.6l-4.5,2.2l-25.5,6.4l-4.1-8.5L93.8,563l-0.9-1.8l4.8-2.3l0.9,1.8l7.6-3.7l2.7,5.6
                  L101.2,566.3z M96.5,568.6l-13.8,6.7l14.8-4.6L96.5,568.6z"
                />
                <path class="st60" d="M76.9,527.2l8.4-2.8l0.3-0.1c1.2-0.4,1.7-1,1.5-1.7c-0.2-0.7-1-0.9-2.3-0.5l-0.2,0.1l-8.4,2.8l-0.4,0.1
                  c-1.1,0.4-1.5,1.1-1.1,2.2l0.1,0.3l0.5,1.6l-5,1.6l-0.4-1.3c-0.3-1-0.6-1.6-1-1.8c-0.3-0.2-0.9-0.1-1.9,0.2l-0.5,0.2l-5,1.6
                  l-0.6,0.2c-1.1,0.4-1.5,0.9-1.3,1.7s1,0.9,2.2,0.5l0.5-0.2l5.6-1.8l2.1,6.3l-5.9,1.9l-1,0.3c-1.4,0.5-2.4,0.7-3.2,0.7
                  c-0.8,0-1.5-0.3-2.3-0.8c-0.8-0.6-1.5-1.5-2-2.7c-0.1-0.2-0.2-0.5-0.4-1c-0.1-0.4-0.3-1-0.5-1.7c-0.3-0.9-0.5-1.7-0.7-2.3
                  c-0.2-0.6-0.3-1.2-0.3-1.7c0-0.5,0-0.9,0-1.3c0.1-0.4,0.2-0.8,0.4-1.1c0.3-0.7,0.8-1.3,1.4-1.7c0.6-0.4,1.5-0.8,2.9-1.3l0.7-0.2
                  l5-1.6l0.6-0.2c1.4-0.4,2.5-0.6,3.3-0.5c0.8,0.2,1.6,0.7,2.5,1.6c0.2-1.3,0.6-2.2,1.2-2.8c0.6-0.6,1.6-1.2,3.2-1.7l7.9-2.6l0.9-0.3
                  c1.5-0.5,2.7-0.7,3.6-0.6c0.9,0.1,1.7,0.5,2.5,1.1c0.2,0.2,0.5,0.4,0.6,0.7c0.2,0.2,0.4,0.5,0.6,0.9s0.4,0.8,0.6,1.4
                  c0.2,0.6,0.4,1.2,0.7,2c0.5,1.6,0.8,2.8,0.9,3.7c0.1,0.8,0,1.6-0.2,2.3c-0.3,0.8-0.8,1.4-1.4,1.9s-1.4,0.9-2.5,1.3l-0.6,0.2
                  l-9.4,3.1L76.9,527.2z"
                />
                <path class="st60" d="M60.8,516.6l1,3.6l-5.1,1.4c-0.6-1.5-1.4-2.5-2.4-3c-1-0.5-2.3-0.7-4-0.5l-1.4-5.1l37.7-10.5l1.8,6.4
                  L60.8,516.6z"
                />
                <path class="st60" d="M64.2,458.7l5.6-0.7l0.4,0c0.9-0.1,1.6-0.3,1.9-0.5c0.3-0.2,0.5-0.5,0.4-1s-0.3-0.7-0.6-0.8s-1-0.2-1.8,0
                  l-0.5,0.1l-9,1.1c0.8,0.7,1.3,1.2,1.5,1.7c0.3,0.5,0.4,1,0.5,1.7c0.2,1.7-0.3,3.1-1.4,4.3c-0.5,0.5-1.1,0.9-1.7,1.2
                  c-0.7,0.3-1.5,0.5-2.6,0.6l-0.6,0.1L46,467.5c-1.4,0.2-2.5,0.2-3.2,0.1s-1.3-0.4-2-1c-0.4-0.3-0.6-0.6-0.9-0.9s-0.4-0.7-0.6-1.2
                  s-0.3-1-0.4-1.7c-0.1-0.7-0.2-1.5-0.4-2.5c-0.2-1.6-0.3-2.8-0.3-3.5c0-0.8,0.2-1.5,0.5-2c0.5-0.9,1-1.5,1.8-2s1.8-0.7,3.1-0.9
                  l0.8-0.1l25.6-3.1l0.5-0.1c1.2-0.2,2.2-0.2,3,0c0.7,0.1,1.4,0.4,1.9,0.9c0.7,0.6,1.1,1.3,1.5,2c0.3,0.8,0.6,1.9,0.8,3.5
                  c0.1,1.1,0.2,2,0.3,2.8c0,0.7,0,1.4,0,1.9s-0.1,1-0.3,1.3s-0.3,0.7-0.6,1c-0.5,0.7-1,1.1-1.7,1.4s-1.5,0.5-2.6,0.7l-0.5,0.1
                  l-7.4,0.9L64.2,458.7z M55,459.8l0.2,0c1.4-0.2,2-0.6,1.9-1.4c0-0.4-0.2-0.7-0.6-0.8c-0.3-0.1-0.9-0.2-1.7-0.1l-8.9,1.1
                  c-0.8,0.1-1.4,0.3-1.7,0.5c-0.3,0.2-0.4,0.5-0.4,1c0.1,0.8,0.8,1.1,2.2,0.9l0.2,0L55,459.8z"
                />
                <path class="st60" d="M69,413.5l-17.5-9.1c-0.7-0.4-1.1-0.6-1.3-0.6l-1.1,0l-5.4-0.2l-0.4,0c-1.1,0-1.7,0.3-1.8,1.1
                  c0,0.8,0.5,1.2,1.5,1.2l0.4,0l6.3,0.3l-0.3,6.6l-7-0.3l-1.1,0c-1.3,0-2.3-0.3-3-0.8c-0.8-0.5-1.3-1.2-1.7-2.2
                  c-0.2-0.5-0.4-1.1-0.4-1.8s0-1.6,0-2.9c0-0.9,0.1-1.7,0.1-2.4c0-0.7,0.1-1.2,0.2-1.7c0.1-0.4,0.3-0.8,0.4-1.1s0.4-0.6,0.6-0.9
                  c0.6-0.7,1.3-1.2,2-1.5c0.8-0.2,1.9-0.3,3.4-0.3l5.4,0.2c1.5,0.1,2.7,0.2,3.5,0.4c0.9,0.2,1.9,0.6,3,1.2l15.1,8.1l0.4-8.8l5.3,0.2
                  l-0.6,15.6L69,413.5z"
                />
                <path class="st60" d="M69.8,394.9l-17-10.1c-0.7-0.4-1.1-0.6-1.3-0.6l-1.1-0.1l-5.4-0.5l-0.4,0c-1.1-0.1-1.7,0.2-1.8,1
                  c-0.1,0.8,0.4,1.2,1.5,1.3l0.4,0l6.3,0.6l-0.6,6.6l-7-0.6l-1-0.1c-1.3-0.1-2.2-0.4-3-1c-0.7-0.5-1.3-1.3-1.6-2.3
                  c-0.2-0.5-0.3-1.1-0.3-1.8c0-0.7,0-1.6,0.2-2.9c0.1-0.9,0.2-1.7,0.3-2.4s0.2-1.2,0.3-1.6c0.1-0.4,0.3-0.8,0.5-1.1
                  c0.2-0.3,0.4-0.6,0.6-0.9c0.6-0.7,1.3-1.1,2.1-1.3c0.8-0.2,2-0.2,3.4-0.1l5.4,0.5c1.5,0.1,2.6,0.4,3.5,0.6c0.9,0.3,1.8,0.7,2.9,1.3
                  l14.7,8.9l0.8-8.8l5.3,0.5l-1.5,15.5L69.8,394.9z"
                />
                <path class="st60" d="M52.8,346.6l-0.7,3.7l-5.2-1c0.1-1.6-0.2-2.9-0.8-3.8s-1.8-1.6-3.4-2.2l1-5.2l38.4,7.5l-1.3,6.5L52.8,346.6z" />
                <path class="st60" d="M63,334.6c-0.4,0.5-0.8,0.9-1.1,1.1s-0.8,0.5-1.2,0.6c-0.4,0.1-0.9,0.2-1.5,0.1c-0.5,0-1.2-0.1-1.9-0.3
                  l-0.5-0.1l-5.4-1.3l-1.2-0.3c-1.3-0.3-2.3-0.8-3-1.4c-0.7-0.6-1.1-1.4-1.4-2.5c-0.1-0.6-0.2-1.2-0.1-1.9s0.3-1.9,0.7-3.4
                  c0.4-1.6,0.8-2.8,1.1-3.5c0.3-0.7,0.8-1.3,1.3-1.8c0.7-0.6,1.5-0.9,2.3-1c0.8-0.1,1.9,0,3.2,0.4l0.5,0.1l5.8,1.4l0.5,0.1
                  c1.3,0.3,2.2,0.8,2.8,1.4c0.6,0.6,1.1,1.5,1.4,2.6c0.8-1,1.7-1.5,2.5-1.8c0.8-0.2,1.9-0.1,3.3,0.2l0.9,0.2l7.9,1.9l0.9,0.2
                  c1.2,0.3,2.1,0.7,2.8,1.2c0.6,0.5,1.1,1.2,1.4,2.1c0.1,0.3,0.2,0.6,0.3,1c0,0.3,0,0.7,0,1.2c0,0.5-0.1,1-0.2,1.6s-0.3,1.4-0.5,2.2
                  c-0.4,1.8-0.9,3.1-1.3,3.9c-0.4,0.8-1,1.4-1.8,1.9c-0.7,0.4-1.3,0.6-2,0.6c-0.7,0-1.8-0.2-3.2-0.5l-7.5-1.8l-1.2-0.3
                  c-0.8-0.2-1.4-0.4-1.9-0.6c-0.5-0.2-1-0.5-1.3-0.8c-0.4-0.3-0.6-0.7-0.8-1.1S63.2,335.3,63,334.6z M53.8,326.1l-0.3-0.1
                  c-1.1-0.3-1.7,0-1.9,0.8s0.3,1.3,1.4,1.5l0.3,0.1l6.2,1.5l0.3,0.1c1.1,0.2,1.8-0.1,1.9-0.7c0.2-0.8-0.3-1.4-1.4-1.5l-0.3-0.1
                  L53.8,326.1z M69.4,329.9l-0.3-0.1c-1.3-0.3-2.1-0.1-2.3,0.7s0.4,1.3,1.7,1.6l0.3,0.1l8.2,2l0.3,0.1c1.3,0.3,2,0,2.2-0.7
                  c0.2-0.8-0.4-1.3-1.7-1.6l-0.2,0L69.4,329.9z"
                />
                <path class="st60" d="M87.7,301.9l-13.9-14c-0.5-0.6-0.9-0.9-1.1-1l-1-0.4l-5.1-1.8l-0.4-0.1c-1.1-0.4-1.7-0.2-2,0.5
                  c-0.3,0.7,0.1,1.3,1.1,1.6l0.4,0.1l5.9,2.1l-2.3,6.2l-6.6-2.4l-1-0.4c-1.2-0.4-2.1-1-2.6-1.7c-0.6-0.7-0.9-1.6-1-2.6
                  c0-0.6,0-1.2,0.1-1.8c0.1-0.6,0.5-1.6,0.9-2.8c0.3-0.9,0.6-1.6,0.8-2.3s0.5-1.1,0.7-1.5c0.2-0.4,0.5-0.7,0.7-1s0.5-0.5,0.8-0.7
                  c0.8-0.5,1.6-0.8,2.4-0.8c0.8,0,1.9,0.3,3.3,0.8L73,280c1.4,0.5,2.5,1,3.2,1.5c0.8,0.5,1.6,1.2,2.5,2l12,12.2l3-8.3l5,1.8
                  l-5.3,14.7L87.7,301.9z"
                />
                <path class="st60" d="M89.7,275.2l5.3,2.2l0.3,0.1c0.9,0.4,1.5,0.5,1.9,0.5c0.4,0,0.7-0.2,0.8-0.6c0.2-0.4,0.1-0.7-0.1-1
                  s-0.8-0.6-1.5-0.9l-0.4-0.2l-8.3-3.5c0.3,0.9,0.5,1.7,0.5,2.2c0,0.5-0.2,1.1-0.4,1.7c-0.6,1.5-1.8,2.6-3.3,3
                  c-0.7,0.2-1.4,0.2-2.1,0.1c-0.7-0.1-1.6-0.3-2.6-0.8l-0.6-0.3l-9.5-4c-1.3-0.6-2.3-1.1-2.8-1.5s-0.9-1-1.2-1.8
                  c-0.2-0.4-0.3-0.8-0.3-1.2s0-0.8,0.1-1.3s0.3-1,0.5-1.6s0.5-1.4,0.9-2.4c0.6-1.5,1.1-2.5,1.5-3.2c0.4-0.7,0.9-1.2,1.4-1.5
                  c0.8-0.5,1.6-0.8,2.5-0.8c0.8,0,1.9,0.3,3.1,0.8l0.7,0.3l23.7,10l0.5,0.2c1.1,0.5,2,1,2.6,1.4c0.6,0.5,1,1,1.2,1.7
                  c0.3,0.8,0.4,1.7,0.3,2.5c-0.1,0.8-0.5,2-1.1,3.4c-0.4,1-0.8,1.9-1.1,2.5c-0.3,0.7-0.6,1.2-1,1.6s-0.6,0.8-0.9,1
                  c-0.3,0.3-0.6,0.5-1,0.6c-0.7,0.3-1.5,0.5-2.2,0.4c-0.7,0-1.6-0.3-2.6-0.7l-0.4-0.2l-6.9-2.9L89.7,275.2z M81.3,271.6l0.2,0.1
                  c1.3,0.5,2,0.4,2.4-0.3c0.2-0.4,0.1-0.7-0.1-1s-0.7-0.6-1.5-0.9L74,266c-0.8-0.3-1.3-0.5-1.7-0.4c-0.4,0-0.6,0.3-0.8,0.7
                  c-0.3,0.7,0.2,1.4,1.4,1.9l0.2,0.1L81.3,271.6z"
                />
                <path class="st60" d="M98.4,215.9l-4.1,6.6l-4.5-2.8l7.6-12.1l5.7,3.5l23.2,24.2l-3.5,5.7L98.4,215.9z" />
                <path class="st60" d="M143.1,200.4l-8.4-17.9c-0.3-0.7-0.5-1.1-0.7-1.3l-0.9-0.7l-4.2-3.4l-0.3-0.3c-0.9-0.7-1.6-0.8-2.1-0.2
                  s-0.3,1.2,0.5,1.9l0.3,0.3l4.9,4L128,188l-5.4-4.5l-0.8-0.7c-1-0.8-1.6-1.6-1.9-2.5s-0.3-1.8,0-2.8c0.1-0.5,0.4-1.1,0.8-1.7
                  c0.4-0.6,1-1.3,1.8-2.3c0.6-0.7,1.1-1.3,1.5-1.8s0.8-0.9,1.2-1.2c0.4-0.3,0.7-0.5,1-0.7c0.3-0.1,0.6-0.3,1-0.4
                  c0.9-0.2,1.7-0.2,2.5,0.1c0.8,0.3,1.7,0.9,2.9,1.9l4.2,3.4c1.1,1,2,1.8,2.5,2.5c0.5,0.7,1.1,1.6,1.7,2.7l7.2,15.6l5.6-6.8l4.1,3.4
                  l-9.9,12L143.1,200.4z"
                />
                <path class="st60" d="M146.1,173.8c-0.6,0.2-1.1,0.4-1.6,0.4c-0.5,0.1-0.9,0.1-1.4,0s-0.9-0.3-1.3-0.6c-0.5-0.3-1-0.7-1.5-1.2
                  l-0.4-0.4l-4.1-3.7l-0.9-0.8c-1-0.9-1.7-1.8-2-2.7s-0.3-1.8,0-2.8c0.1-0.6,0.4-1.1,0.9-1.7s1.2-1.5,2.3-2.6c1.1-1.2,2-2.1,2.7-2.6
                  c0.6-0.5,1.3-0.8,2-0.9c0.9-0.1,1.7-0.1,2.4,0.3s1.6,0.9,2.6,1.9l0.4,0.3l4.4,4l0.4,0.4c1,0.9,1.5,1.7,1.8,2.6c0.3,0.9,0.2,1.8,0,3
                  c1.2-0.4,2.2-0.5,3-0.3c0.8,0.2,1.8,0.8,2.8,1.8l0.6,0.6l6,5.5l0.6,0.6c0.9,0.9,1.5,1.6,1.9,2.4c0.3,0.8,0.4,1.6,0.3,2.5
                  c0,0.3-0.1,0.7-0.3,1c-0.1,0.3-0.3,0.7-0.6,1c-0.3,0.4-0.6,0.8-1,1.3c-0.4,0.5-0.9,1.1-1.5,1.7c-1.2,1.4-2.2,2.3-3,2.7
                  c-0.8,0.5-1.6,0.7-2.5,0.7c-0.8,0-1.5-0.1-2-0.5c-0.6-0.3-1.4-1-2.5-2l-5.7-5.2l-0.9-0.8c-0.6-0.5-1-1-1.4-1.4s-0.6-0.9-0.8-1.3
                  s-0.2-0.9-0.2-1.4C145.9,175,146,174.4,146.1,173.8z M142.2,161.9l-0.3-0.2c-0.8-0.7-1.5-0.8-2-0.3c-0.5,0.6-0.4,1.3,0.5,2l0.2,0.2
                  l4.7,4.3l0.2,0.2c0.9,0.7,1.6,0.8,2,0.3c0.6-0.6,0.4-1.3-0.5-2l-0.2-0.2L142.2,161.9z M154,172.7l-0.3-0.2c-1-0.9-1.8-1.1-2.3-0.5
                  c-0.5,0.6-0.3,1.3,0.7,2.2l0.3,0.2l6.2,5.7l0.3,0.2c1,0.9,1.8,1,2.3,0.5c0.5-0.6,0.3-1.3-0.7-2.3l-0.1-0.1L154,172.7z"
                />
                <path class="st60" d="M175.3,134.5l-2.9,2.4l-3.4-4c1.1-1.2,1.7-2.3,1.7-3.5c0-1.1-0.4-2.4-1.2-3.9l4-3.4l25.3,29.9l-5.1,4.3
                  L175.3,134.5z"
                />
                <path class="st60" d="M197.5,145.2l-4.2-19.3c-0.1-0.8-0.3-1.2-0.4-1.4l-0.7-0.9l-3.3-4.3l-0.3-0.3c-0.7-0.9-1.4-1.1-2-0.6
                  s-0.6,1.1,0,2l0.3,0.3l3.9,5l-5.2,4.1l-4.3-5.6l-0.6-0.8c-0.8-1-1.2-1.9-1.3-2.8c-0.1-0.9,0.1-1.8,0.6-2.8c0.3-0.5,0.6-1,1.1-1.4
                  c0.5-0.5,1.2-1.1,2.3-1.9c0.7-0.6,1.4-1.1,1.9-1.4s1-0.7,1.4-0.9c0.4-0.2,0.8-0.3,1.1-0.4s0.7-0.1,1.1-0.1c0.9,0,1.7,0.2,2.4,0.6
                  c0.7,0.4,1.5,1.3,2.4,2.4l3.3,4.3c0.9,1.2,1.6,2.2,1.9,3s0.7,1.8,1,3l3.6,16.8l7-5.4l3.2,4.2l-12.4,9.6L197.5,145.2z"
                />
                <path class="st60" d="M233.1,106.3l4.4,7.7l0.1,0.3c0.6,1.1,1.3,1.5,2,1.1s0.7-1.2,0-2.3l-0.1-0.2l-4.4-7.7l-0.2-0.4
                  c-0.6-1-1.4-1.2-2.3-0.6l-0.3,0.2l-1.5,0.9l-2.6-4.6l1.2-0.7c0.9-0.5,1.4-1,1.5-1.3c0.1-0.3-0.1-0.9-0.6-1.8l-0.3-0.5l-2.6-4.5
                  l-0.3-0.5c-0.6-1-1.2-1.3-1.9-0.9s-0.7,1.1,0,2.3l0.2,0.4l2.9,5.1l-5.7,3.3l-3.1-5.4l-0.5-0.9c-0.8-1.2-1.2-2.2-1.3-3
                  c-0.1-0.8,0-1.6,0.3-2.4c0.4-0.9,1.1-1.7,2.2-2.5c0.2-0.1,0.5-0.3,0.9-0.6s0.9-0.5,1.5-0.9c0.8-0.5,1.5-0.9,2.1-1.2
                  c0.6-0.3,1.1-0.5,1.6-0.7s0.9-0.2,1.3-0.2c0.4,0,0.8,0.1,1.2,0.1c0.8,0.2,1.4,0.5,1.9,1c0.5,0.5,1.1,1.3,1.9,2.5L233,88l2.6,4.6
                  l0.3,0.5c0.7,1.3,1.1,2.3,1.1,3.1s-0.3,1.8-1,2.8c1.3-0.1,2.3,0.1,3,0.6c0.7,0.4,1.5,1.4,2.3,2.8l4.2,7.2l0.5,0.8
                  c0.8,1.4,1.2,2.5,1.3,3.4c0.1,0.8-0.1,1.7-0.6,2.6c-0.2,0.3-0.3,0.5-0.5,0.8c-0.2,0.3-0.5,0.5-0.8,0.8c-0.3,0.3-0.7,0.5-1.2,0.9
                  c-0.5,0.3-1.1,0.7-1.8,1.1c-1.5,0.8-2.6,1.4-3.4,1.6c-0.8,0.3-1.6,0.4-2.3,0.3c-0.9-0.1-1.6-0.4-2.2-0.9s-1.2-1.2-1.8-2.2l-0.3-0.5
                  l-5-8.6L233.1,106.3z"
                />
                <path class="st60" d="M249.9,97.1l4,7.9c0.4,0.7,0.7,1.2,1,1.4c0.3,0.2,0.7,0.3,1,0.1c0.4-0.2,0.6-0.5,0.6-0.8
                  c0-0.4-0.2-0.9-0.5-1.6l-0.1-0.2l-6.3-12.4c-0.3-0.6-0.6-1-0.9-1.2c-0.3-0.2-0.6-0.1-1,0.1c-0.7,0.4-0.9,0.9-0.5,1.7l0.2,0.5
                  l0.6,1.2l-5.9,3l-8.6-16.9l13.9-7.1l2.4,4.7l-8,4.1l3.1,6.2c0.5-1.5,1.3-2.6,2.6-3.3c1.5-0.8,2.9-0.8,4.2-0.1
                  c1.3,0.7,2.5,1.9,3.5,3.8l0.3,0.6l6.6,13l0.5,0.9c1,2.1,1.2,3.8,0.4,5.1c-0.4,0.7-0.9,1.2-1.5,1.7c-0.6,0.5-1.6,1.1-3.1,1.8
                  c-0.7,0.4-1.3,0.6-1.8,0.9c-0.5,0.2-1,0.4-1.4,0.6c-0.4,0.1-0.8,0.2-1.1,0.3c-0.3,0.1-0.7,0.1-1,0.1c-1.9,0-3.3-1-4.3-3.1l-0.5-0.9
                  l-4.5-8.8L249.9,97.1z"
                />
                <path class="st60" d="M296.8,78.2l2.9,8.4l0.1,0.3c0.4,1.2,1,1.7,1.8,1.4s0.9-1,0.5-2.3l-0.1-0.2l-2.9-8.4l-0.1-0.4
                  c-0.4-1.1-1.1-1.5-2.2-1.1l-0.3,0.1l-1.6,0.6l-1.7-5l1.3-0.5c1-0.4,1.6-0.7,1.7-1c0.2-0.3,0.1-0.9-0.2-1.8l-0.2-0.5l-1.7-4.9
                  l-0.2-0.6c-0.4-1.1-1-1.5-1.7-1.3c-0.7,0.3-0.9,1-0.5,2.2l0.2,0.4l1.9,5.5l-6.3,2.2l-2-5.9l-0.3-0.9c-0.5-1.4-0.8-2.4-0.7-3.2
                  c0-0.8,0.3-1.6,0.7-2.3c0.5-0.8,1.4-1.5,2.6-2c0.2-0.1,0.5-0.3,1-0.4c0.4-0.1,1-0.4,1.7-0.6c0.9-0.3,1.7-0.6,2.3-0.7
                  c0.6-0.2,1.2-0.3,1.7-0.4c0.5-0.1,0.9-0.1,1.3,0s0.8,0.2,1.2,0.3c0.7,0.3,1.3,0.8,1.7,1.3c0.4,0.6,0.9,1.5,1.4,2.8l0.3,0.7l1.7,5
                  l0.2,0.6c0.5,1.4,0.7,2.5,0.5,3.3s-0.6,1.7-1.5,2.6c1.3,0.2,2.2,0.6,2.8,1.1s1.2,1.6,1.7,3.1l2.8,7.9l0.3,0.9
                  c0.5,1.5,0.7,2.7,0.7,3.6c-0.1,0.9-0.4,1.7-1.1,2.5c-0.2,0.2-0.4,0.5-0.7,0.7c-0.2,0.2-0.5,0.4-0.9,0.6s-0.8,0.4-1.4,0.6
                  c-0.5,0.2-1.2,0.4-2,0.7c-1.6,0.6-2.8,0.9-3.6,1c-0.8,0.1-1.6,0.1-2.3-0.2c-0.8-0.3-1.5-0.7-2-1.3c-0.5-0.6-0.9-1.4-1.3-2.5
                  l-0.2-0.6l-3.3-9.4L296.8,78.2z"
                />
                <path class="st60" d="M338.3,76.6l4.7-19.2c0.2-0.8,0.3-1.2,0.3-1.4l-0.2-1.1l-1.1-5.3l-0.1-0.4c-0.2-1.1-0.7-1.6-1.5-1.4
                  s-1,0.8-0.8,1.8l0.1,0.4l1.3,6.1l-6.5,1.3l-1.4-6.9l-0.2-1c-0.3-1.2-0.2-2.3,0-3.1c0.3-0.9,0.9-1.6,1.7-2.2c0.5-0.3,1-0.6,1.6-0.8
                  c0.6-0.2,1.6-0.4,2.8-0.7c0.9-0.2,1.7-0.3,2.4-0.4c0.7-0.1,1.2-0.2,1.7-0.2c0.5,0,0.9,0.1,1.2,0.1c0.3,0.1,0.7,0.2,1,0.4
                  c0.8,0.4,1.5,0.9,1.9,1.6c0.4,0.7,0.8,1.8,1.1,3.3l1.1,5.3c0.3,1.5,0.4,2.6,0.4,3.5s-0.2,2-0.4,3.2l-4.2,16.6l8.6-1.8l1.1,5.2
                  l-15.3,3.1L338.3,76.6z"
                />
                <path class="st60" d="M362.7,52.3l-1-6.4c-0.2-1.2-0.7-1.7-1.4-1.6c-0.8,0.1-1.1,0.7-0.9,1.7l0.1,0.4l1.4,9.4
                  c0.6-0.6,1.2-1.1,1.7-1.4c0.5-0.3,1.1-0.5,1.8-0.6c1.6-0.2,3,0.2,4.1,1.2c1.1,1.1,1.8,2.6,2.1,4.7l0.1,0.5l1.5,10l0.2,1.1
                  c0.3,2.6-0.4,4.4-2.3,5.4c-0.3,0.2-0.5,0.3-0.8,0.4c-0.2,0.1-0.5,0.2-0.9,0.3s-0.8,0.2-1.3,0.3c-0.5,0.1-1.2,0.2-1.9,0.3
                  c-0.9,0.1-1.6,0.2-2.2,0.3c-0.6,0.1-1.1,0.1-1.6,0.1c-0.4,0-0.8-0.1-1.2-0.2c-0.3-0.1-0.7-0.3-1-0.4c-0.8-0.5-1.4-1-1.8-1.7
                  c-0.4-0.7-0.6-1.7-0.8-3l-0.1-0.8L353,47.7l-0.2-1.2c-0.3-1.7-0.2-3.1,0.1-4s1-1.7,2.1-2.3c0.2-0.1,0.4-0.3,0.7-0.4
                  c0.2-0.1,0.5-0.2,0.9-0.3c0.3-0.1,0.8-0.1,1.3-0.3c0.5-0.1,1.1-0.2,1.8-0.3c1.6-0.2,2.8-0.4,3.6-0.4c0.8,0,1.5,0.1,2.1,0.4
                  c0.8,0.4,1.5,0.9,2,1.7c0.5,0.7,0.8,1.8,1,3l0.1,0.5l1,7L362.7,52.3z M364.1,61.3c-0.1-0.8-0.3-1.3-0.5-1.6c-0.2-0.3-0.5-0.4-1-0.3
                  c-0.4,0.1-0.7,0.3-0.8,0.6s-0.1,0.9,0,1.6l1.3,8.8c0.1,0.9,0.3,1.6,0.5,1.9c0.2,0.3,0.5,0.4,1,0.4c0.5-0.1,0.8-0.3,0.8-0.7
                  c0.1-0.4,0.1-1,0-1.9l-0.1-0.4L364.1,61.3z"
                />

                <radialGradient id="SVGID_39_" cx="414.5" cy="414.54" r="411.5" gradientTransform="matrix(1 0 0 -1 0 829.04)" gradientUnits="userSpaceOnUse">
                  <stop offset="0.9397" style="stop-color:#130831;stop-opacity:0" />
                  <stop offset="0.9668" style="stop-color:#0A0419;stop-opacity:0.3146" />
                  <stop offset="1" style="stop-color:#000000;stop-opacity:0.7" />
                </radialGradient>
                <path class="st61" d="M822.5,360.5c-3.1-23.7-8.2-46.7-15.2-68.9c-14-44.8-35.4-86.3-62.9-123c-13.9-18.6-29.3-36-46-51.9
                  c-33.2-31.6-71.7-57.8-114-77C532.6,16.1,475.1,3,414.5,3S296.4,16.1,244.6,39.6c-42.7,19.4-81.6,45.9-115,78
                  c-49.4,47.4-87,107-107.9,174c-6.9,22.2-12.1,45.3-15.2,68.9c-2.3,17.7-3.5,35.7-3.5,54c0,29.5,3.1,58.2,9,85.9
                  c9.8,46.4,27.5,89.9,51.6,129.1c48.8,79.5,123.8,141.1,213,172.8c43.1,15.3,89.5,23.7,137.9,23.7c98.1,0,188.2-34.3,258.9-91.6
                  c36.3-29.4,67.5-64.9,92-104.9c24-39.2,41.7-82.7,51.6-129.1c5.9-27.7,9-56.4,9-85.9C826,396.2,824.8,378.2,822.5,360.5z"
                />
                <path class="st62" d="M716.8,375.3c-2.3-17.5-6.1-34.5-11.2-50.8c-5.1-16.1-11.4-31.7-19-46.5c-7.9-15.5-17.1-30.3-27.5-44.1
                  c-10.2-13.6-21.5-26.4-33.8-38.1c-12.3-11.7-25.5-22.4-39.7-31.9c-14-9.5-28.9-17.8-44.5-24.9c-15.5-7-31.7-12.8-48.5-17.2
                  c-16.6-4.3-33.7-7.3-51.2-8.7c-8.4-0.7-16.9-1.1-25.5-1.1c-8.5,0-17,0.4-25.3,1c-17.4,1.4-34.5,4.4-50.9,8.6
                  c-17,4.4-33.5,10.3-49.2,17.4c-15.5,7.1-30.3,15.4-44.3,24.8c-14.4,9.7-27.9,20.6-40.4,32.6c-12.2,11.7-23.4,24.4-33.6,38
                  c-10.2,13.7-19.3,28.3-27.1,43.7c-7.5,14.8-13.8,30.3-18.9,46.3c-5.1,16.4-8.9,33.4-11.2,50.8c-1.7,13.1-2.6,26.5-2.6,40.1
                  c0,3.6,0.1,7.2,0.2,10.8c0.6,17.9,2.8,35.3,6.4,52.3c3.6,17.1,8.7,33.6,15,49.6c6.4,16,14.1,31.4,23.1,45.9
                  c8.9,14.4,18.9,28.1,30,40.8c11.4,13.1,24,25.3,37.5,36.3c13.2,10.7,27.3,20.3,42.1,28.8c15.1,8.5,30.9,15.8,47.4,21.7
                  c16.1,5.7,32.8,10.1,50,13.1c16.8,2.9,34.1,4.4,51.7,4.4c17.9,0,35.4-1.5,52.4-4.5c16.8-2.9,33-7.2,48.8-12.8
                  c16.5-5.8,32.2-13,47.3-21.4c15.1-8.5,29.3-18.2,42.7-29c13.4-10.9,25.9-22.9,37.2-35.8c11.3-12.9,21.6-26.9,30.7-41.6
                  c9-14.7,16.8-30.2,23.2-46.3c6.2-15.7,11.2-32,14.8-48.9c3.5-16.7,5.7-34,6.4-51.6c0.1-3.8,0.2-7.7,0.2-11.5
                  C719.4,401.8,718.5,388.4,716.8,375.3z"
                />

                <circle cx="414.55" cy="414.65" r="60" class="circle-center-outer" />
                <rect x="258" y="404" width="110" height="25" class="cross-arm-1" />
                <rect x="458" y="404" width="110" height="25" class="cross-arm-2" />
                <rect x="401" y="258" width="25" height="110" class="cross-arm-3" />
                <rect x="401" y="461" width="25" height="110" class="cross-arm-4" />

                <circle cx="258" cy="416.5" r="30" class="cross-circle-1" />
                <circle cx="568" cy="416.5" r="30" class="cross-circle-2" />
                <circle cx="413.5" cy="258" r="30" class="cross-circle-3" />
                <circle cx="413.5" cy="571" r="30" class="cross-circle-4" />

                <circle cx="258" cy="416.5" r="20" class="cross-circle-inner-1" />
                <circle cx="568" cy="416.5" r="20" class="cross-circle-inner-2" />
                <circle cx="413.5" cy="258" r="20" class="cross-circle-inner-3" />
                <circle cx="413.5" cy="571" r="20" class="cross-circle-inner-4" />

                <circle cx="414.55" cy="414.65" r="50" class="circle-center" />
                <circle cx="414.55" cy="414.65" r="30" class="circle-center-inner" />

              </g>
            </svg>
          </object>
          <object class="outside">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 456 456">
              <radialGradient id="roulette_svg_outside" cx="228" cy="227.76" r="225.5" gradientTransform="matrix(1 0 0 -1 0 455.7599)" gradientUnits="userSpaceOnUse">
                <stop class="gradient g1" offset="0.8945" />
                <stop class="gradient g2" offset="0.901" />
                <stop class="gradient g3" offset="0.9166" />
                <stop class="gradient g4" offset="0.9387" />
                <stop class="gradient g5" offset="1" />
              </radialGradient>
              <path class="st0" :style="`fill:`+($vuetify.theme.isDark?'#303030':'white')" d="M451.6,198.5c-1.7-13-4.5-25.5-8.3-37.7c-3.7-12-8.4-23.5-14-34.5c-5.8-11.5-12.6-22.5-20.3-32.8
              c-7.5-10.1-15.8-19.5-24.9-28.2c-9.1-8.7-18.9-16.7-29.4-23.8c-10.5-7.1-21.5-13.4-33.1-18.6c-11.5-5.3-23.5-9.6-36-12.8
              c-12.4-3.3-25.2-5.5-38.4-6.6c-6.3-0.5-12.8-0.8-19.2-0.8c-6.3,0-12.5,0.3-18.6,0.8c-13,1-25.6,3.2-37.9,6.4
              c-12.7,3.3-24.9,7.6-36.6,12.9c-11.5,5.2-22.6,11.4-33,18.5c-10.7,7.2-20.7,15.4-30,24.3c-9.1,8.7-17.5,18.2-25,28.4
              c-7.6,10.2-14.4,21.1-20.1,32.6c-5.5,11-10.2,22.4-14,34.3c-3.8,12.2-6.6,24.9-8.3,37.9C3.2,208.1,2.5,218,2.5,228
              c0,2.8,0,5.5,0.2,8.3c0.5,13.3,2.1,26.3,4.8,38.9c2.7,12.7,6.5,25,11.3,36.9c4.8,12,10.6,23.5,17.3,34.3
              c6.6,10.7,14.1,20.8,22.4,30.2c8.5,9.7,17.9,18.7,28,26.9c9.8,7.9,20.3,15,31.3,21.3c11.2,6.3,23,11.7,35.3,16
              c12,4.2,24.4,7.5,37.1,9.6c12.3,2.1,24.9,3.2,37.8,3.2h0.5c13.3,0,26.2-1.2,38.8-3.4c12.4-2.2,24.5-5.4,36.2-9.5
              c12.2-4.3,23.9-9.7,35-16c11.2-6.3,21.7-13.5,31.6-21.5c9.9-8.1,19.2-17,27.6-26.6c8.4-9.6,16-19.9,22.7-30.8
              c6.7-10.9,12.4-22.4,17.2-34.3c4.6-11.6,8.3-23.8,11-36.3c2.6-12.4,4.2-25.2,4.7-38.2c0.1-2.9,0.2-5.8,0.2-8.8
              C453.5,218,452.9,208.2,451.6,198.5z"
              />
              <path class="st0" style="fill:url(#roulette_svg_outside);stroke-width:5;stroke-miterlimit:10;" d="M451.6,198.5c-1.7-13-4.5-25.5-8.3-37.7c-3.7-12-8.4-23.5-14-34.5c-5.8-11.5-12.6-22.5-20.3-32.8
              c-7.5-10.1-15.8-19.5-24.9-28.2c-9.1-8.7-18.9-16.7-29.4-23.8c-10.5-7.1-21.5-13.4-33.1-18.6c-11.5-5.3-23.5-9.6-36-12.8
              c-12.4-3.3-25.2-5.5-38.4-6.6c-6.3-0.5-12.8-0.8-19.2-0.8c-6.3,0-12.5,0.3-18.6,0.8c-13,1-25.6,3.2-37.9,6.4
              c-12.7,3.3-24.9,7.6-36.6,12.9c-11.5,5.2-22.6,11.4-33,18.5c-10.7,7.2-20.7,15.4-30,24.3c-9.1,8.7-17.5,18.2-25,28.4
              c-7.6,10.2-14.4,21.1-20.1,32.6c-5.5,11-10.2,22.4-14,34.3c-3.8,12.2-6.6,24.9-8.3,37.9C3.2,208.1,2.5,218,2.5,228
              c0,2.8,0,5.5,0.2,8.3c0.5,13.3,2.1,26.3,4.8,38.9c2.7,12.7,6.5,25,11.3,36.9c4.8,12,10.6,23.5,17.3,34.3
              c6.6,10.7,14.1,20.8,22.4,30.2c8.5,9.7,17.9,18.7,28,26.9c9.8,7.9,20.3,15,31.3,21.3c11.2,6.3,23,11.7,35.3,16
              c12,4.2,24.4,7.5,37.1,9.6c12.3,2.1,24.9,3.2,37.8,3.2h0.5c13.3,0,26.2-1.2,38.8-3.4c12.4-2.2,24.5-5.4,36.2-9.5
              c12.2-4.3,23.9-9.7,35-16c11.2-6.3,21.7-13.5,31.6-21.5c9.9-8.1,19.2-17,27.6-26.6c8.4-9.6,16-19.9,22.7-30.8
              c6.7-10.9,12.4-22.4,17.2-34.3c4.6-11.6,8.3-23.8,11-36.3c2.6-12.4,4.2-25.2,4.7-38.2c0.1-2.9,0.2-5.8,0.2-8.8
              C453.5,218,452.9,208.2,451.6,198.5z"
              />
            </svg>
          </object>
        </div>
      </div>
      <div class="bets d-flex flex-column justify-center align-center" :class="{'bet-ready':!playing}">
        <div id="rolls" class="mb-5 text-no-wrap overflow-hidden">
          <transition-group name="slide-x-reverse-transition" tag="div">
            <v-chip
              v-for="roll in rolls"
              :key="roll.time"
              x-small
              label
              :color="roll.color"
              text-color="#fff"
              class="mr-1"
            >
              {{ roll.number }}
            </v-chip>
          </transition-group>
        </div>
        <div class="bets-btns">
          <div v-if="!bettingFinals" class="inner">
            <!-- ZERO -->
            <button :data-betsize="bets['straight0']?bets['straight0']:null" class="bet v3 bnm" :class="{win:win_number==0, active:active.indexOf(0)!=-1, hover:hover.indexOf(0)!=-1}" @click="placeBet('straight0')" @contextmenu="e => {e.preventDefault(); removeBet('straight0')}">
              <span>0</span>
            </button>
            <!-- COLUMN 3 -->
            <div class="bets-sup">
              <button v-for="i in 12" :key="`col-3-street-${i}`" :data-betsize="bets['street'+(i*3-2)]?bets['street'+(i*3-2)]:null" class="betm vt hm" @click="placeBet('street'+(i*3-2))" @contextmenu="e => {e.preventDefault(); removeBet('street'+(i*3-2))}" @mousedown="active=[i*3-2,i*3-1,i*3-0]" @mouseup="active=[]" @mouseover="hover=[i*3-2,i*3-1,i*3-0]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 11" :key="`col-3-sixline-${i}`" :data-betsize="bets['sixline'+(i*3-2)]?bets['sixline'+(i*3-2)]:null" class="betm vt hr szc" @click="placeBet('sixline'+(i*3-2))" @contextmenu="e => {e.preventDefault(); removeBet('sixline'+(i*3-2))}" @mousedown="active=[i*3-2,i*3-1,i*3-0,i*3+3,i*3+2,i*3+1]" @mouseup="active=[]" @mouseover="hover=[i*3-2,i*3-1,i*3-0,i*3+3,i*3+2,i*3+1]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 11" :key="`col-3-split-${i}`" :data-betsize="bets[`split${i*3-0}_${i*3+3}`]?bets[`split${i*3-0}_${i*3+3}`]:null" class="betm vm hr szhs" @click="placeBet(`split${i*3-0}_${i*3+3}`)" @contextmenu="e => {e.preventDefault(); removeBet(`split${i*3-0}_${i*3+3}`)}" @mousedown="active=[i*3-0,i*3+3]" @mouseup="active=[]" @mouseover="hover=[i*3-0,i*3+3]" @mouseout="hover=[]" />
            </div>
            <button v-for="i in 12" :key="'col-3-bet-'+i" :data-betsize="bets[`straight${i*3-0}`]?bets[`straight${i*3-0}`]:null" class="bet" :class="{red:columns_color[i*3-0]=='r',black:columns_color[i*3-0]!='r',win:win_number==i*3-0, active:active.indexOf(i*3-0)!=-1,hover:hover.indexOf(i*3-0)!=-1}" @click="placeBet(`straight${i*3-0}`)" @contextmenu="e => {e.preventDefault(); removeBet(`straight${i*3-0}`)}">
              <span>{{ i*3 - 0 }}</span>
            </button>
            <button class="bet" :data-betsize="bets['column3']?bets['column3']:null" @click="placeBet('column3')" @contextmenu="e => {e.preventDefault(); removeBet('column3')}" @mousedown="active=[];for(var i=3;i<37;i+=3)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=3;i<37;i+=3)hover.push(i);" @mouseout="hover=[]">
              <span>{{ $t('2:1') }}</span>
            </button>
            <div class="break" />
            <!-- COLUMN 2 -->
            <div class="bets-sup">
              <button :data-betsize="bets['trio23']?bets['trio23']:null" class="betm vb hl szc " @click="placeBet('trio23')" @contextmenu="e => {e.preventDefault(); removeBet('trio23')}" @mousedown="active=[0,2,3]" @mouseup="active=[]" @mouseover="hover=[0,2,3]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 12" :key="`col-2-split-${i}`" :data-betsize="bets[`split${i*3-1}_${i*3-0}`]?bets[`split${i*3-1}_${i*3-0}`]:null" class="betm vt hm szvs" @click="placeBet(`split${i*3-1}_${i*3-0}`)" @contextmenu="e => {e.preventDefault(); removeBet(`split${i*3-1}_${i*3-0}`)}" @mousedown="active=[i*3-1,i*3-0]" @mouseup="active=[]" @mouseover="hover=[i*3-1,i*3-0]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 11" :key="`col-2-corner-${i}`" :data-betsize="bets['corner'+(i*3-1)]?bets['corner'+(i*3-1)]:null" class="betm vt hr szc" @click="placeBet('corner'+(i*3-1))" @contextmenu="e => {e.preventDefault(); removeBet('corner'+(i*3-1))}" @mousedown="active=[i*3-1,i*3,i*3+2,i*3+3]" @mouseup="active=[]" @mouseover="hover=[i*3-1,i*3,i*3+2,i*3+3]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 11" :key="`col-2-split-${i}`" :data-betsize="bets[`split${i*3-1}_${i*3+2}`]?bets[`split${i*3-1}_${i*3+2}`]:null" class="betm vm hr szhs" @click="placeBet(`split${i*3-1}_${i*3+2}`)" @contextmenu="e => {e.preventDefault(); removeBet(`split${i*3-1}_${i*3+2}`)}" @mousedown="active=[i*3-1,i*3+2]" @mouseup="active=[]" @mouseover="hover=[i*3-1,i*3+2]" @mouseout="hover=[]" />
            </div>
            <div v-for="i in 12" :key="`col-2-bet-${i}`" :data-betsize="bets[`straight${i*3-1}`]?bets[`straight${i*3-1}`]:null" class="bet" :class="{red:columns_color[i*3-1]=='r',black:columns_color[i*3-1]!='r',win:win_number==i*3-1, active:active.indexOf(i*3-1)!=-1,hover:hover.indexOf(i*3-1)!=-1}" @click="placeBet(`straight${i*3-1}`)" @contextmenu="e => {e.preventDefault(); removeBet(`straight${i*3-1}`)}">
              <span>{{ i*3 - 1 }}</span>
            </div>
            <div :data-betsize="bets['column2']?bets['column2']:null" class="bet" @click="placeBet('column2')" @contextmenu="e => {e.preventDefault(); removeBet('column2')}" @mousedown="active=[];for(var i=2;i<36;i+=3)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=2;i<36;i+=3)hover.push(i);" @mouseout="hover=[]">
              <span>{{ $t('2:1') }}</span>
            </div>
            <div class="break" />
            <!-- COLUMN 1 -->
            <div class="bets-sup">
              <button :data-betsize="bets['trio12']?bets['trio12']:null" class="betm vb hl szc " @click="placeBet('trio12')" @contextmenu="e => {e.preventDefault(); removeBet('trio12')}" @mousedown="active=[0,1,2]" @mouseup="active=[]" @mouseover="hover=[0,1,2]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 12" :key="`col-1-split-${i}`" :data-betsize="bets[`split${i*3-2}_${i*3-1}`]?bets[`split${i*3-2}_${i*3-1}`]:null" class="betm vt hm szvs" @click="placeBet(`split${i*3-2}_${i*3-1}`)" @contextmenu="e => {e.preventDefault(); removeBet(`split${i*3-2}_${i*3-1}`)}" @mousedown="active=[i*3-2,i*3-1]" @mouseup="active=[]" @mouseover="hover=[i*3-2,i*3-1]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 11" :key="`col-1-corner-${i}`" :data-betsize="bets['corner'+(i*3-2)] ?bets['corner'+(i*3-2)] :null" class="betm vt hr szc" @click="placeBet('corner'+(i*3-2))" @contextmenu="e => {e.preventDefault(); removeBet('corner'+(i*3-2))}" @mousedown="active=[i*3-2,i*3-1,i*3+1,i*3+2]" @mouseup="active=[]" @mouseover="hover=[i*3-2,i*3-1,i*3+1,i*3+2]" @mouseout="hover=[]" />
            </div>
            <div class="bets-sup">
              <button v-for="i in 11" :key="`col-1-split-${i}`" :data-betsize="bets[`split${i*3-2}_${i*3+1}`]?bets[`split${i*3-2}_${i*3+1}`]:null" class="betm vm hr szhs" @click="placeBet(`split${i*3-2}_${i*3+1}`)" @contextmenu="e => {e.preventDefault(); removeBet(`split${i*3-2}_${i*3+1}`)}" @mousedown="active=[i*3-2,i*3+1]" @mouseup="active=[]" @mouseover="hover=[i*3-2,i*3+1]" @mouseout="hover=[]" />
            </div>
            <button v-for="i in 12" :key="'col-1-bet-'+i" :data-betsize="bets[`straight${i*3-2}`]?bets[`straight${i*3-2}`]:null" class="bet" :class="{red:columns_color[i*3-2]=='r',black:columns_color[i*3-2]!='r',win:win_number==i*3-2, active:active.indexOf(i*3-2)!=-1,hover:hover.indexOf(i*3-2)!=-1}" @click="placeBet(`straight${i*3-2}`)" @contextmenu="e => {e.preventDefault(); removeBet(`straight${i*3-2}`)}">
              <span>{{ i*3 - 2 }}</span>
            </button>
            <button :data-betsize="bets['column1']?bets['column1']:null" class="bet" @click="placeBet('column1')" @contextmenu="e => {e.preventDefault(); removeBet('column1')}" @mousedown="active=[];for(var i=1;i<35;i+=3)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<35;i+=3)hover.push(i);" @mouseout="hover=[]">
              <span>{{ $t('2:1') }}</span>
            </button>
            <div class="break" />
            <!-- BOTTOM BTNS LINE 1-->
            <div class="bets-sup">
              <button :data-betsize="bets['top_line']?bets['top_line']:null" class="betm vb hl szc " @click="placeBet('top_line')" @contextmenu="e => {e.preventDefault(); removeBet('top_line')}" @mousedown="active=[0,1,2,3]" @mouseup="active=[]" @mouseover="hover=[0,1,2,3]" @mouseout="hover=[]" />
            </div>
            <button :data-betsize="bets['dozen1']?bets['dozen1'] :null" class="bet ml1 h4" @mousedown="active=[];for(var i=1;i<13;i++)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<13;i++)hover.push(i);" @mouseout="hover=[]" @click="placeBet('dozen1')" @contextmenu="e => {e.preventDefault(); removeBet('dozen1')}">
              <span>{{ $t('1st 12') }}</span>
            </button>
            <button :data-betsize="bets['dozen2']?bets['dozen2'] :null" class="bet h4" @mousedown="active=[];for(var i=1;i<13;i++)active.push(i+12);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<13;i++)hover.push(i+12);" @mouseout="hover=[]" @click="placeBet('dozen2')" @contextmenu="e => {e.preventDefault(); removeBet('dozen2')}">
              <span>{{ $t('2nd 12') }}</span>
            </button>
            <button :data-betsize="bets['dozen3']?bets['dozen3'] :null" class="bet mr1 h4" @mousedown="active=[];for(var i=1;i<13;i++)active.push(i+24);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<13;i++)hover.push(i+24);" @mouseout="hover=[]" @click="placeBet('dozen3')" @contextmenu="e => {e.preventDefault(); removeBet('dozen3')}">
              <span>{{ $t('3rd 12') }}</span>
            </button>
            <div class="break" />
            <!-- BOTTOM BTNS LINE 2-->
            <button :data-betsize="bets['low'] ?bets['low'] :null" class="bet ml1 h2" @mousedown="active=[];for(var i=1;i<19;i++)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<19;i++)hover.push(i);" @mouseout="hover=[]" @click="placeBet('low')" @contextmenu="e => {e.preventDefault(); removeBet('low')}">
              <span>{{ $t('1 to 18') }}</span>
            </button>
            <button :data-betsize="bets['even'] ?bets['even'] :null" class="bet h2" @mousedown="active=[];for(var i=2;i<37;i+=2)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=2;i<37;i+=2)hover.push(i);" @mouseout="hover=[]" @click="placeBet('even')" @contextmenu="e => {e.preventDefault(); removeBet('even')}">
              <span>{{ $t('Even') }}</span>
            </button>
            <button :data-betsize="bets['red'] ?bets['red'] :null" class="bet h2 red" @mousedown="active=[];for(var i=1;i<37;i++)if(columns_color[i]=='r')active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<37;i++)if(columns_color[i]=='r')hover.push(i);" @mouseout="hover=[]" @click="placeBet('red')" @contextmenu="e => {e.preventDefault(); removeBet('red')}">
              <span>{{ $t('Red') }}</span>
            </button>
            <button :data-betsize="bets['black'] ?bets['black'] :null" class="bet h2 black" @mousedown="active=[];for(var i=1;i<37;i++)if(columns_color[i]=='b')active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<37;i++)if(columns_color[i]=='b')hover.push(i);" @mouseout="hover=[]" @click="placeBet('black')" @contextmenu="e => {e.preventDefault(); removeBet('black')}">
              <span>{{ $t('Black') }}</span>
            </button>
            <button :data-betsize="bets['odd'] ?bets['odd'] :null" class="bet h2" @mousedown="active=[];for(var i=1;i<37;i+=2)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=1;i<37;i+=2)hover.push(i);" @mouseout="hover=[]" @click="placeBet('odd')" @contextmenu="e => {e.preventDefault(); removeBet('odd')}">
              <span>{{ $t('Odd') }}</span>
            </button>
            <button :data-betsize="bets['high'] ?bets['high'] :null" class="bet mr1 h2" @mousedown="active=[];for(var i=19;i<37;i++)active.push(i);" @mouseup="active=[]" @mouseover="hover=[];for(var i=19;i<37;i++)hover.push(i);" @mouseout="hover=[]" @click="placeBet('high')" @contextmenu="e => {e.preventDefault(); removeBet('high')}">
              <span>{{ $t('19 to 36') }}</span>
            </button>
            <div class="fixer" />
          </div>
          <div v-if="bettingFinals" class="inner">
            <div class="break" />
            <div class="bet filler" />
            <div class="bet filler" />
            <v-btn v-for="i in [0,1,2,3,4]" :key="'btn-finals-'+i" :disabled="playing" color="primary" outlined class="bet h2" @click="placeFinals(i)">
              {{ i }}
            </v-btn>
            <div class="bet filler" />
            <div class="bet filler" />
            <div class="break" />
            <div class="bet filler" />
            <div class="bet filler" />
            <v-btn v-for="i in [5,6,7,8,9]" :key="'btn-finals-'+i" :disabled="playing" color="primary" outlined class="bet h2" @click="placeFinals(i)">
              {{ i }}
            </v-btn>
            <div class="break" />
            <div v-for="i in 6" :key="`filler${i}`" class="bet filler" />
            <v-btn :disabled="playing" color="primary" class="h2" @click="bettingFinals=!bettingFinals">
              {{ $t('Cancel') }}
            </v-btn>
            <div class="break" />
            <div class="break" />
            <div class="fixer" />
          </div>
        </div>
        <div class="d-md-flex text-center justify-center flex-wrap mt-1">
          <v-btn :disabled="playing" small class="mx-1 my-2 my-lg-0" @click="placeVoisins()">
            {{ $t('Voisins du Zéro') }}
          </v-btn>
          <v-btn :disabled="playing" small class="mx-1 my-2 my-lg-0" @click="placeTiers()">
            {{ $t('Tiers') }}
          </v-btn>
          <v-btn :disabled="playing" small class="mx-1 my-2 my-lg-0" @click="placeOrphelins()">
            {{ $t('Orphelins') }}
          </v-btn>
          <br>
          <v-btn :disabled="playing" small class="mx-1 my-2 my-lg-0" @click.stop="placeNeighbours()">
            {{ $t('Neighbours') }}
          </v-btn>
          <v-btn :disabled="playing" small class="mx-1 my-2 my-lg-0" @click="bettingFinals=!bettingFinals;win_number=-1;">
            {{ $t('Finals') }}
          </v-btn>
        </div>
      </div>
    </div>
    <div class="d-flex justify-center flex-wrap">
      <v-btn
        :disabled="playing || Object.keys(bets_old).length === 0"
        class="mx-1 my-2 my-lg-0"
        small
        @click="repeat"
      >
        {{ $t('Repeat') }}
      </v-btn>
      <v-btn
        :disabled="playing || Object.keys(bets).length === 0"
        class="mx-1 my-2 my-lg-0"
        small
        @click="double"
      >
        {{ $t('Double') }}
      </v-btn>
      <v-btn
        :disabled="playing || Object.keys(bets).length === 0"
        class="mx-1 my-2 my-lg-0"
        small
        @click="clear"
      >
        {{ $t('Clear') }}
      </v-btn>
    </div>
    <play-controls :loading="loading" :playing="playing" :disabled="account.balance < totalBetAmount || totalBetAmount === 0 " @bet-change="bet = $event" @play="play">
      <template v-slot:after-bet-input>
        <v-text-field
          :label="$t('Total bet')"
          :value="totalBetAmount"
          dense
          :readonly="!playing"
          :disabled="playing"
          outlined
          :full-width="false"
          class="total-bet-input text-center ml-2"
        />
      </template>
    </play-controls>
  </div>
</template>

<script>
  import axios from 'axios'
  import vClickOutside from 'v-click-outside'
  import { mapState, mapActions } from 'vuex'
  import { config } from '~/plugins/config'
  import FormMixin from '~/mixins/Form'
  import GameMixin from '~/mixins/Game'
  import SoundMixin from '~/mixins/Sound'
  import clickSound from '~/../audio/common/click.wav'
  import winSound from 'packages/european-roulette/resources/audio/win.wav'
  import loseSound from 'packages/european-roulette/resources/audio/lose.wav'

  import betSound from 'packages/european-roulette/resources/audio/bet.wav'
  import unbetSound from 'packages/european-roulette/resources/audio/unbet.wav'
  import noneSound from 'packages/european-roulette/resources/audio/none.wav'
  import spinSound from 'packages/european-roulette/resources/audio/spin.wav'
  import startSound from 'packages/european-roulette/resources/audio/start.wav'
  import stopSound from 'packages/european-roulette/resources/audio/stop.wav'

  import PlayControls from '~/components/Games/PlayControls'
  import GameMessage from '~/components/Games/GameMessage'
  import ballImg from 'packages/european-roulette/resources/images/ball.png'

  export default {
    name: 'EuropeanRoulette',

    components: { GameMessage, PlayControls },

    directives: {
      clickOutside: vClickOutside.directive
    },

    mixins: [FormMixin, GameMixin, SoundMixin],

    data () {
      return {
        clickSound,
        betSound,
        unbetSound,
        noneSound,
        spinSound,
        startSound,
        stopSound,
        formIsValid: true,
        loading: false,
        playing: false,
        bets: {},
        bet: 0,
        win: 0,
        max_bet: parseInt(config('european-roulette.max_bet'), 10),
        min_bet: parseInt(config('european-roulette.min_bet'), 10),
        rolls: [],
        ready: false,
        message: null,
        bets_roulette: {},
        bets_old: {},
        bets_roulette_old: {},
        hover: [],
        active: [],
        win_number: -1,
        game_result: null,
        bettingFinals: false,
        bettingRoulette: false,
        rouletteLights: [],
        img: {
          roulette: null,
          roulette_inside: null,
          roulette_outside: null,
          ball: null
        },
        animation: {
          wheel_angle: 0
        },
        animationTimer: null,
        rouletteNums: [0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26],
        columns_color: [
          0,
          'r', 'b', 'r',
          'b', 'r', 'b',
          'r', 'b', 'r',
          'b', 'b', 'r',

          'b', 'r', 'b',
          'r', 'b', 'r',
          'r', 'b', 'r',
          'b', 'r', 'b',

          'r', 'b', 'r',
          'b', 'b', 'r',
          'b', 'r', 'b',
          'r', 'b', 'r'
        ]
      }
    },
    computed: {
      ...mapState('auth', ['account']),
      nonZeroBets () {
        return Object.keys(this.bets)
          .filter(key => this.bets[key] > 0)
          .reduce((a, key) => { return { ...a, [key]: this.bets[key] } }, {})
      },
      totalBetAmount () {
        return Object.keys(this.bets).reduce((result, key) => { result += this.bets[key]; return result }, 0)
      }
    },

    watch: {
      bettingRoulette (isBettingRoulette) {
        if (isBettingRoulette) {
          this.message = this.$t('Choose numbers on the roulette')
        } else {
          this.message = null
        }
      }
    },

    created () {
      if ('hidden' in document) {
        document.addEventListener('visibilitychange', this.changeWindowState)
      } else if ('mozHidden' in document) {
        document.addEventListener('mozvisibilitychange', this.changeWindowState)
      } else if ('webkitHidden' in document) {
        document.addEventListener('webkitvisibilitychange', this.changeWindowState)
      } else if ('msHidden' in document) {
        document.addEventListener('msvisibilitychange', this.changeWindowState)
      }
    },
    mounted () {
      window.tmp = this
      this.ready = false
      this.img.ball = new Image()
      this.img.ball.onload = () => { this.ready = true; this.startAnimation() }
      this.img.ball.src = ballImg
    },
    beforeDestroy () {
      this.stopAnimation()
    },
    methods: {
      ...mapActions({
        updateUserAccountBalance: 'auth/updateUserAccountBalance',
        setProvablyFairGame: 'provably-fair/set'
      }),
      play () {
        this.actions = []
        this.sound(clickSound)
        this.message = null
        this.game_result = null
        this.animation.start()
        this.loading = true
        this.playing = true
        this.win = 0
        this.updateUserAccountBalance(this.account.balance - this.totalBetAmount)
        // API request params
        const endpoint = this.getRoute('play')
        const requestParams = { hash: this.provablyFairGame.hash, bets: this.nonZeroBets }
        // execute the action
        // const { data: game } = await axios.post(endpoint, requestParams)
        axios.post(endpoint, requestParams).then(response => {
          const game = response.data
          this.loading = false
          this.setProvablyFairGame({ key: this.gamePackageId, game: game.pf_game })
          this.game_result = {
            ball_idx: this.rouletteNums.indexOf(game.gameable.win_number),
            ball_num: game.gameable.win_number,
            win: parseFloat(game.win),
            balance: game.account.balance
          }
        })
      },
      showResult () {
        this.playing = false
        this.updateUserAccountBalance(this.game_result.balance)
        this.bets_old = Object.assign({}, this.bets)
        this.bets_roulette_old = Object.assign({}, this.bets_roulette)
        this.bets = {}
        this.bets_roulette = {}
        this.win_number = this.game_result.ball_num
        if (this.rolls.length >= 10) {
          this.rolls.splice(0, 1)
        }
        this.rolls.push({
          number: this.game_result.ball_num,
          color: this.game_result.ball_idx % 2 === 1 ? 'red' : (this.game_result.ball_idx % 2 === 0 ? 'black' : 'green'),
          time: new Date().getTime() // time serves as a unique key for the transition group
        })
        if (this.game_result.win) {
          this.message = this.$t('You won') + ' ' + this.game_result.win
          this.sound(winSound)
          this.win = this.game_result.win
        } else {
          this.sound(loseSound)
        }
      },
      double () {
        for (var j in this.bets) {
          if (this.account.balance >= this.bets[j] * 2) {
            this.$set(this.bets, j, this.bets[j] * 2 > this.max_bet ? this.max_bet : this.bets[j] * 2)
          } else {
            this.sound(noneSound)
            return
          }
        }
        this.sound(betSound)
      },
      clear () {
        this.sound(unbetSound)
        this.bets = {}
      },
      placeBet (pos) {
        this.win_number = -1
        if (this.playing) {
          this.sound(noneSound)
          return
        }
        if ((this.bets[pos] ? this.bets[pos] : 0) + this.bet <= this.max_bet) {
          this.sound(betSound)
          this.$set(this.bets, pos, (this.bets[pos] ? this.bets[pos] : 0) + this.bet)
        } else { this.sound(noneSound) }
      },
      removeBet (pos) {
        this.win_number = -1
        if (this.playing) {
          this.sound(noneSound)
          return
        }
        if (this.bets[pos] && this.bets[pos] > 0) {
          this.sound(betSound)
          this.$set(this.bets, pos, Math.max(this.bets[pos] - this.bet, 0))
          if (!this.bets[pos])
            this.$delete(this.bets, pos)
        } else { this.sound(noneSound) }
      },
      rouletteBet (e) {
        if (!this.playing && this.bettingRoulette) {
          const sz = this.$refs.roulette.clientWidth / this.$refs.roulette.width
          const x = e.offsetX / sz - this.$refs.roulette.width / 2
          const y = e.offsetY / sz - this.$refs.roulette.width / 2
          const w = Math.sqrt(x * x + y * y)
          var a = 0
          var num = -1
          var bets = []
          if (w > 150 && w < 200) {
            if (x > 0) { a = Math.atan(y / x) + Math.PI * 0.5 - this.animation.wheel_angle } else { a = Math.atan(y / x) + Math.PI * 1.5 - this.animation.wheel_angle }
            while (a < 0)a += Math.PI * 2
            num = Math.round(a / (Math.PI * 2 / 37))
            while (num > 37)num -= 37
            while (num < 0)num += 37
            if (num === 37)num = 0
            var isAllow = true
            bets = [
              num - 2,
              num - 1,
              num,
              num + 1,
              num + 2
            ]
            bets.forEach((el) => {
              if (el < 0) { el = 37 + el }
              if (el > 37) { el = el - 37 }
              if (el === 37) { el = 0 }
              if ((this.bets['straight' + this.rouletteNums[el]] ? this.bets['straight' + this.rouletteNums[el]] : 0) + this.bet > this.max_bet) { isAllow = false }
            })
            if (!isAllow) {
              this.sound(noneSound)
              return
            }
            bets.forEach((el) => {
              if (el < 0) { el = 37 + el }
              if (el > 37) { el = el - 37 }
              if (el === 37) { el = 0 }
              el = 'straight' + this.rouletteNums[el]
              this.$set(this.bets, el, (this.bets[el] ? this.bets[el] : 0) + this.bet)
            })
            if (num === 37)num = 0
            this.$set(this.bets_roulette, num, (this.bets_roulette[num] ? this.bets_roulette[num] : 0) + this.bet)
            this.sound(betSound)
          }
        }
      },
      endRouletteLight () {
        this.rouletteLights.forEach(el => { this.$refs['rouletteCell' + el].classList.remove('light') })
        this.rouletteLights = []
      },
      rouletteMouse (e) {
        if (!this.playing && this.bettingRoulette) {
          const sz = this.$refs.roulette.clientWidth / this.$refs.roulette.width
          const x = e.offsetX / sz - this.$refs.roulette.width / 2
          const y = e.offsetY / sz - this.$refs.roulette.width / 2
          const w = Math.sqrt(x * x + y * y)
          var a = 0
          var num = -1
          if (w > 150 && w < 200) {
            if (x > 0) { a = Math.atan(y / x) + Math.PI * 0.5 - this.animation.wheel_angle } else { a = Math.atan(y / x) + Math.PI * 1.5 - this.animation.wheel_angle }
            while (a < 0)a += Math.PI * 2
            num = Math.round(a / (Math.PI * 2 / 37))
            while (num > 37)num -= 37
            while (num < 0)num += 37
            if (num === 37)num = 0
            this.rouletteLights.forEach(el => { this.$refs['rouletteCell' + el].classList.remove('light') })
            this.rouletteLights = [
              num - 2,
              num - 1,
              num,
              num + 1,
              num + 2
            ]
            this.rouletteLights.forEach((el, i) => {
              if (el < 0) this.rouletteLights[i] = 37 + el
              if (el >= 37) this.rouletteLights[i] = el - 37
              if (el === 37) this.rouletteLights[i] = 0
            })
            this.rouletteLights.forEach(el => { this.$refs['rouletteCell' + el].classList.add('light') })
          } else {
            this.endRouletteLight()
          }
        }
      },
      placeVoisins () {
        this.bettingFinals = false
        this.bettingRoulette = false
        this.endRouletteLight()
        this.win_number = -1
        var isAllow = true
        const bets = ['trio23', 'split4_7', 'split12_15', 'split19_22', 'corner25', 'split32_35', 'split18_21']
        bets.forEach(el => (isAllow = isAllow && ((this.bets[el] ? this.bets[el] : 0) + this.bet * (el === 'trio23' || el === 'corner25' ? 2 : 1) <= this.max_bet)))
        if (isAllow) {
          bets.forEach(el => this.$set(this.bets, el, (this.bets[el] ? this.bets[el] : 0) + this.bet * (el === 'trio23' || el === 'corner25' ? 2 : 1)))
          this.sound(betSound)
        } else { this.sound(noneSound) }
      },
      placeTiers () {
        this.bettingFinals = false
        this.bettingRoulette = false
        this.endRouletteLight()
        this.win_number = -1
        var isAllow = true
        const bets = ['split5_8', 'split10_11', 'split13_16', 'split23_24', 'split27_30', 'split33_36']
        bets.forEach(el => (isAllow = isAllow && ((this.bets[el] ? this.bets[el] : 0) + this.bet <= this.max_bet)))
        if (isAllow) {
          bets.forEach(el => this.$set(this.bets, el, (this.bets[el] ? this.bets[el] : 0) + this.bet))
          this.sound(betSound)
        } else { this.sound(noneSound) }
      },
      placeOrphelins () {
        this.bettingFinals = false
        this.bettingRoulette = false
        this.endRouletteLight()
        this.win_number = -1
        var isAllow = true
        const bets = ['straight1', 'split6_9', 'split14_17', 'split17_20', 'split31_34']
        bets.forEach(el => (isAllow = isAllow && ((this.bets[el] ? this.bets[el] : 0) + this.bet <= this.max_bet)))
        if (isAllow) {
          bets.forEach(el => this.$set(this.bets, el, (this.bets[el] ? this.bets[el] : 0) + this.bet))
          this.sound('bet')
        } else { this.sound('none') }
      },
      placeNeighbours () {
        this.win_number = -1
        this.bettingFinals = false
        this.bettingRoulette = !this.bettingRoulette
        this.endRouletteLight()
      },
      placeFinals (i) {
        this.bettingFinals = false
        this.bettingRoulette = false
        this.endRouletteLight()
        const iOri = i
        while (i < 37) {
          if ((this.bets['straight' + i] ? this.bets['straight' + i] : 0) + this.bet > this.max_bet) {
            this.sound(noneSound)
            return
          }
          i += 10
        }
        i = iOri
        while (i < 37) {
          this.$set(this.bets, 'straight' + i, (this.bets['straight' + i] ? this.bets['straight' + i] : 0) + this.bet)
          i += 10
        }
        this.sound(betSound)
      },
      setBet (amount) {
        this.sound(clickSound)
        this.bet = Math.max(Math.min(amount, this.max_bet), this.min_bet)
      },
      repeat () {
        this.win_number = -1
        this.sound(betSound)
        this.bets = Object.assign({}, this.bets_old)
        this.bets_roulette = Object.assign({}, this.bets_roulette_old)
      },
      changeWindowState (e) {
        this.bettingFinals = false
        this.bettingRoulette = false
        this.endRouletteLight()
        if (this.animation && this.animation.requestAnimationFrame_get) {
          this.animation.requestAnimationFrame_get()
          this.animation.requestAnimationFrame(() => {
            if (this.animation.animation_stage >= 7 && this.animation.animation_stage <= 10) { this.animation.stop() }
          })
        }
      },
      startAnimation () {
        this.animation = {
          is_working: false,
          t: Date.now(),
          game_t: Date.now(),
          wheel_angle: 0,
          wheel_speed: 0,
          ball_angle: 0,
          ball_speed: 0,
          ball_position: 120,
          ball_scale: 1,
          animation_stage: 0,

          ball_num: 0,
          ball_idx: 0,

          ball_angle_ori: 0,
          ball_break_dist: 0,
          ball_break_accl: 0,
          ball_break_t: 0,

          requestAnimationFrame: e => setTimeout(e, 1),
          requestAnimationFrame_get: () => {
            var _raf = window.requestAnimationFrame ||
              window.mozRequestAnimationFrame ||
              window.webkitRequestAnimationFrame ||
              window.msRequestAnimationFrame ||
              window.oRequestAnimationFrame

            this.animation.requestAnimationFrame = _raf ? _raf.bind(window) : null
          },
          render: () => {
            if (this.animation.is_working) return
            this.animation.is_working = true
            if (this.$refs.roulette === undefined) return
            const ctx = this.$refs.roulette.getContext('2d')
            this.animation.t = this.animation.game_t
            this.animation.game_t = Date.now()
            ctx.clearRect(0, 0, this.$refs.roulette.width, this.$refs.roulette.height)
            if (this.animation.wheel_speed > 0) {
              this.animation.wheel_angle += (this.animation.game_t - this.animation.t) * this.animation.wheel_speed
              while (this.animation.wheel_angle > Math.PI * 2) this.animation.wheel_angle -= Math.PI * 2
              this.$refs.wheel.style.transform = `rotate(${this.animation.wheel_angle}rad)`
            }
            ctx.save()
            ctx.translate(240, 240)
            ctx.rotate(this.animation.wheel_angle)
            ctx.translate(-240, -240)
            if (this.animation.ball_speed !== 0) {
              this.animation.ball_angle += (this.animation.game_t - this.animation.t) * this.animation.ball_speed
              while (this.animation.ball_angle > Math.PI * 2) this.animation.ball_angle -= Math.PI * 2
              while (this.animation.ball_angle < -Math.PI * 2) this.animation.ball_angle += Math.PI * 2
            }
            ctx.drawImage(this.img.ball,
              240 - Math.sin(this.animation.ball_angle) * this.animation.ball_position - 8 * this.animation.ball_scale,
              240 - Math.cos(this.animation.ball_angle) * this.animation.ball_position - 8 * this.animation.ball_scale,
              16 * this.animation.ball_scale,
              16 * this.animation.ball_scale
            )
            if (this.animation.ball_speed > 0.002) {
              for (var i = 0; i < Math.round(10000 * (this.animation.ball_speed - 0.002) / 3); i++) {
                ctx.globalAlpha = 0.15
                ctx.drawImage(this.img.ball,
                  240 - Math.sin(this.animation.ball_angle - i * 0.02) * this.animation.ball_position - 8 * this.animation.ball_scale,
                  240 - Math.cos(this.animation.ball_angle - i * 0.02) * this.animation.ball_position - 8 * this.animation.ball_scale,
                  16 * this.animation.ball_scale,
                  16 * this.animation.ball_scale
                )
              }
            }
            ctx.globalAlpha = 1
            ctx.restore()
            this.animation.calculate()
            this.animation.is_working = false
          },
          stop: () => {
            this.soundLoop(spinSound)
            this.sound(stopSound)
            this.animation.animation_stage = 11
            this.animation.ball_angle = this.animation.ball_angle_ori + this.animation.ball_break_dist
            this.animation.ball_position = 120
          },
          calculate: () => {
            switch (this.animation.animation_stage) {
              case 1:
                this.animation.ball_speed = this.animation.wheel_speed
                this.animation.ball_position += (this.animation.game_t - this.animation.t) * 0.45
                var p = 100 * (this.animation.ball_position - 120) / (223 - 120)
                if (p > 50) p = 100 - p
                this.animation.ball_scale = 1 + p / 150
                if (this.animation.ball_position >= 223) {
                  this.animation.ball_position = 223
                  this.animation.ball_scale = 1
                  this.animation.animation_stage++
                }
                break
              case 2:
                this.animation.wheel_speed += (this.animation.game_t - this.animation.t) * 0.00001
                if (this.animation.wheel_speed >= 0.001) {
                  this.animation.wheel_speed = 0.001
                  this.animation.animation_stage++
                }
                this.animation.ball_speed = -this.animation.wheel_speed
                break
              case 3:
                this.animation.ball_speed += (this.animation.game_t - this.animation.t) * 0.0001
                if (this.animation.ball_speed >= 0.009) {
                  this.animation.ball_speed = 0.009
                  this.animation.animation_stage++
                }
                break
              case 4:
                if (this.game_result) {
                  this.animation.animation_stage += 2
                  this.animation.ball_idx = this.game_result.ball_idx
                  this.animation.ball_num = this.game_result.ball_num
                }
                break
              case 6: {
                const v = 6 * Math.PI - ((Math.PI * 2 / 37) * this.animation.ball_idx) - this.animation.ball_angle
                this.animation.ball_angle_ori = this.animation.ball_angle
                this.animation.ball_break_dist = v
                this.animation.ball_break_accl = 0.009 * 0.009 / (2 * v)
                this.animation.ball_break_t = this.animation.game_t
                this.animation.animation_stage++
                this.animation.ball_speed = 0
                break }
              case 7: {
                const s = 0.009 * (this.animation.game_t - this.animation.ball_break_t) - this.animation.ball_break_accl * (this.animation.game_t - this.animation.ball_break_t) * (this.animation.game_t - this.animation.ball_break_t) / 2
                this.animation.ball_position = 223 - (s / this.animation.ball_break_dist) * (223 - 140)
                this.animation.ball_angle = this.animation.ball_angle_ori + s
                if (s >= (this.animation.ball_break_dist - (Math.PI * 2 / 37) / 2 - (Math.PI * 2 / 37) * 6)) {
                  this.animation.animation_stage++
                  this.animation.ball_position = 360 - (s / this.animation.ball_break_dist) * (360 - 130)
                  this.soundStop(spinSound)
                  this.sound(stopSound)
                }
                if (s < 0) this.animation.stop()
                break }
              case 8: {
                const s = 0.009 * (this.animation.game_t - this.animation.ball_break_t) - this.animation.ball_break_accl * (this.animation.game_t - this.animation.ball_break_t) * (this.animation.game_t - this.animation.ball_break_t) / 2
                this.animation.ball_position = 360 - (s / this.animation.ball_break_dist) * (360 - 130)
                this.animation.ball_angle = this.animation.ball_angle_ori + s
                if (s >= (this.animation.ball_break_dist - (Math.PI * 2 / 37) / 2 - (Math.PI * 2 / 37) * 2)) {
                  this.animation.animation_stage++
                  this.animation.ball_position = 460 - (s / this.animation.ball_break_dist) * (460 - 125)
                }
                if (s < 0) this.animation.stop()
                break }
              case 9: {
                const s = 0.009 * (this.animation.game_t - this.animation.ball_break_t) - this.animation.ball_break_accl * (this.animation.game_t - this.animation.ball_break_t) * (this.animation.game_t - this.animation.ball_break_t) / 2
                this.animation.ball_position = 360 - (s / this.animation.ball_break_dist) * (360 - 125)
                this.animation.ball_angle = this.animation.ball_angle_ori + s
                if (s >= (this.animation.ball_break_dist - (Math.PI * 2 / 37) / 2 - (Math.PI * 2 / 37) * 1)) {
                  this.animation.animation_stage++
                  this.animation.ball_position = 260 - (s / this.animation.ball_break_dist) * (260 - 120)
                }
                if (s < 0) this.animation.stop()
                break }
              case 10: {
                const s = 0.009 * (this.animation.game_t - this.animation.ball_break_t) - this.animation.ball_break_accl * (this.animation.game_t - this.animation.ball_break_t) * (this.animation.game_t - this.animation.ball_break_t) / 2
                this.animation.ball_position = 260 - (s / this.animation.ball_break_dist) * (260 - 120)
                this.animation.ball_angle = this.animation.ball_angle_ori + s
                if (s >= (this.animation.ball_break_dist - 0.07)) {
                  this.animation.ball_angle = this.animation.ball_angle_ori + this.animation.ball_break_dist
                  this.animation.animation_stage++
                  this.animation.ball_position = 120
                }
                if (s < 0) this.animation.stop()
                break }
              case 11:
                setTimeout(() => { this.showResult() }, 500)
                this.animation.animation_stage++
                break
              case 12:
                this.animation.wheel_speed -= (this.animation.game_t - this.animation.t) * 0.000001
                if (this.animation.wheel_speed <= 0) {
                  this.animation.wheel_speed = 0
                  this.animation.animation_stage++
                }
                break
            }
          },
          start: () => {
            this.sound(startSound)
            this.soundLoop(spinSound)
            this.animation.animation_stage = 1
          }
        }
        this.animation.requestAnimationFrame_get()
        this.animationTimer = setInterval(() => {
          if (!this.animation.is_working) this.animation.requestAnimationFrame(this.animation.render)
        }, 25)
      },
      stopAnimation () {
        clearInterval(this.animationTimer)
      },
      onClickOutsideRoulette (event) {
        this.bettingRoulette = false
        this.endRouletteLight()
      },
      onClickOutsideBets (event) {
        this.bettingFinals = false
      }

    }
  }
</script>

<style lang="scss" scoped>
  #rolls {
    min-height: 25px;
  }

  .total-bet-input {
    max-width: 100px;
  }

  $red: #F44336;//lighten(red, 7%);
  $green: darken(green, 10%);
  $width: calc( (100vw - 32px - 4px*16) / 14 );
  $margin: 4px;

  $width576: 18px;
  $width768: 24px;
  $width992: 34px;
  $width1200: 40px;

  .roulette {
    width:300px;
    height:300px;
    position: relative;
    margin-right: 26px;
    @media screen and (max-width: 575px) { width:300px;height:300px;margin-bottom:16px;margin-right: 0px; }
    @media screen and (min-width: 576px) { width:200px;height:200px;margin-right:16px; }
    @media screen and (min-width: 768px) { width:200px;height:200px; }
    @media screen and (min-width: 992px) { width:200px;height:200px; }
    @media screen and (min-width: 1200px) { width:300px;height:300px; }
    canvas {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%,-50%);
      width: 99%;
      z-index:4;
    }
    .roulette-source {
      width:calc( 406*99%/480 );
      height:calc( 406*99%/480 );
      position: absolute;
      left:50%;
      top:50%;
      display:block;
      z-index:1;
      transform:translate(-50%,-50%);
    }
    .roulette-source svg g {
      .cross-arm-1 {fill:var(--v-primary-darken2);}
      .cross-arm-2 {fill:var(--v-primary-darken2);}
      .cross-arm-3 {fill:var(--v-primary-darken2);}
      .cross-arm-4 {fill:var(--v-primary-darken2);}
      .cross-circle-1 {fill:var(--v-primary-darken2);}
      .cross-circle-2 {fill:var(--v-primary-darken2);}
      .cross-circle-3 {fill:var(--v-primary-darken2);}
      .cross-circle-4 {fill:var(--v-primary-darken2);}

      .cross-circle-inner-1 {fill:var(--v-primary-darken1);}
      .cross-circle-inner-2 {fill:var(--v-primary-darken1);}
      .cross-circle-inner-3 {fill:var(--v-primary-darken1);}
      .cross-circle-inner-4 {fill:var(--v-primary-darken1);}

      .circle-center-outer {fill:var(--v-primary-base);}
      .circle-center {fill:var(--v-primary-darken2);}
      .circle-center-inner {fill:var(--v-primary-base);}

      .st58,.st59,.st61,.st62,
      .st0,.st1,.st2,.st3,.st4,.st5,.st6,.st7,.st8,.st9,.st10,.st11,.st12,.st13,.st14,.st15,.st16,.st17,
      .st18,.st19,.st20,
      .st21,.st22,.st23,.st24,.st25,.st26,.st27,.st28,.st29,.st30,.st31,.st32,.st33,.st34,.st35,.st36,.st37,.st38,.st39,.st40,.st41,.st42,.st43,.st44,.st45,.st46,.st47,.st48,.st49,.st50,.st51,.st52,.st53,.st54,.st55,.st56,.st57 {
        stroke:var(--v-primary-base);
      }
      .st59 {
        fill: transparent;
      }
      .circle-inner-gradient.g1 {stop-color:var(--v-primary-base);stop-opacity:0.00;}
      .circle-inner-gradient.g2 {stop-color:var(--v-primary-base);stop-opacity:0.00;}
      .circle-inner-gradient.g3 {stop-color:var(--v-primary-base);stop-opacity:0.10;}
      .circle-inner-gradient.g4 {stop-color:var(--v-primary-base);stop-opacity:0.25;}
      .circle-inner-gradient.g5 {stop-color:var(--v-primary-base);}

      .st0{fill:none;stroke-width:5.0968;stroke-miterlimit:10;}
      .st1{fill:none;stroke-width:5.1037;stroke-miterlimit:10;}
      .st2{fill:none;stroke-width:5.0481;stroke-miterlimit:10;}
      .st3{fill:none;stroke-width:5;stroke-miterlimit:10;}
      .st4{fill:none;stroke-width:5.0674;stroke-miterlimit:10;}
      .st5{fill:none;stroke-width:5.0476;stroke-miterlimit:10;}
      .st6{fill:none;stroke-width:5.0202;stroke-miterlimit:10;}
      .st7{fill:none;stroke-width:5.0375;stroke-miterlimit:10;}
      .st8{fill:none;stroke-width:5.0253;stroke-miterlimit:10;}
      .st9{fill:none;stroke-width:5.0573;stroke-miterlimit:10;}
      .st10{fill:none;stroke-width:5.0642;stroke-miterlimit:10;}
      .st11{fill:none;stroke-width:5.0415;stroke-miterlimit:10;}
      .st12{fill:none;stroke-width:5.0593;stroke-miterlimit:10;}
      .st13{fill:none;stroke-width:4.6425;stroke-miterlimit:10;}
      .st14{fill:none;stroke-width:5.0599;stroke-miterlimit:10;}
      .st15{fill:none;stroke-width:5.0581;stroke-miterlimit:10;}
      .st16{fill:none;stroke-width:5.0754;stroke-miterlimit:10;}
      .st17{fill:none;stroke-width:5.0757;stroke-miterlimit:10;}

      .st18{
        fill: #000;
        stroke-width:5;stroke-miterlimit:10;
        transition: fill .4s ease;

        &.light {
          fill: #9c9c9c;
        }
      }
      .st19{
        fill:$green;stroke-width:5;stroke-miterlimit:10;
        transition: fill .4s ease;
        &.light{
          fill:lighten($green,20%);
        }
      }
      .st20{
        fill:$red;stroke-width:5;stroke-miterlimit:10;
        transition: fill .4s ease;
        &.light{
          fill:lighten($red,20%);
        }
      }

      .st21{fill:url(#SVGID_1_);stroke-width:5;stroke-miterlimit:10;}
      .st22{fill:url(#SVGID_2_);stroke-width:5;stroke-miterlimit:10;}
      .st23{fill:url(#SVGID_3_);stroke-width:5;stroke-miterlimit:10;}
      .st24{fill:url(#SVGID_4_);stroke-width:5;stroke-miterlimit:10;}
      .st25{fill:url(#SVGID_5_);stroke-width:5;stroke-miterlimit:10;}
      .st26{fill:url(#SVGID_6_);stroke-width:5;stroke-miterlimit:10;}
      .st27{fill:url(#SVGID_7_);stroke-width:5;stroke-miterlimit:10;}
      .st28{fill:url(#SVGID_8_);stroke-width:5;stroke-miterlimit:10;}
      .st29{fill:url(#SVGID_9_);stroke-width:5;stroke-miterlimit:10;}
      .st30{fill:url(#SVGID_10_);stroke-width:5;stroke-miterlimit:10;}
      .st31{fill:url(#SVGID_11_);stroke-width:5;stroke-miterlimit:10;}
      .st32{fill:url(#SVGID_12_);stroke-width:5;stroke-miterlimit:10;}
      .st33{fill:url(#SVGID_13_);stroke-width:5;stroke-miterlimit:10;}
      .st34{fill:url(#SVGID_14_);stroke-width:5;stroke-miterlimit:10;}
      .st35{fill:url(#SVGID_15_);stroke-width:5;stroke-miterlimit:10;}
      .st36{fill:url(#SVGID_16_);stroke-width:5;stroke-miterlimit:10;}
      .st37{fill:url(#SVGID_17_);stroke-width:5;stroke-miterlimit:10;}
      .st38{fill:url(#SVGID_18_);stroke-width:5;stroke-miterlimit:10;}
      .st39{fill:url(#SVGID_19_);stroke-width:5;stroke-miterlimit:10;}
      .st40{fill:url(#SVGID_20_);stroke-width:5;stroke-miterlimit:10;}
      .st41{fill:url(#SVGID_21_);stroke-width:5;stroke-miterlimit:10;}
      .st42{fill:url(#SVGID_22_);stroke-width:5;stroke-miterlimit:10;}
      .st43{fill:url(#SVGID_23_);stroke-width:5;stroke-miterlimit:10;}
      .st44{fill:url(#SVGID_24_);stroke-width:5;stroke-miterlimit:10;}
      .st45{fill:url(#SVGID_25_);stroke-width:5;stroke-miterlimit:10;}
      .st46{fill:url(#SVGID_26_);stroke-width:5;stroke-miterlimit:10;}
      .st47{fill:url(#SVGID_27_);stroke-width:5;stroke-miterlimit:10;}
      .st48{fill:url(#SVGID_28_);stroke-width:5;stroke-miterlimit:10;}
      .st49{fill:url(#SVGID_29_);stroke-width:5;stroke-miterlimit:10;}
      .st50{fill:url(#SVGID_30_);stroke-width:5;stroke-miterlimit:10;}
      .st51{fill:url(#SVGID_31_);stroke-width:5;stroke-miterlimit:10;}
      .st52{fill:url(#SVGID_32_);stroke-width:5;stroke-miterlimit:10;}
      .st53{fill:url(#SVGID_33_);stroke-width:5;stroke-miterlimit:10;}
      .st54{fill:url(#SVGID_34_);stroke-width:5;stroke-miterlimit:10;}
      .st55{fill:url(#SVGID_35_);stroke-width:5;stroke-miterlimit:10;}
      .st56{fill:url(#SVGID_36_);stroke-width:5;stroke-miterlimit:10;}
      .st57{fill:url(#SVGID_37_);stroke-width:5;stroke-miterlimit:10;}

      .st58{fill:url(#SVGID_38_);stroke-width:5;stroke-miterlimit:10;}

      .st59{stroke-width:7.959;stroke-miterlimit:10;}
      .st60{fill:#ffffff;}
      .st61{fill:url(#SVGID_39_);stroke-width:6;stroke-miterlimit:10;}
      .st62{fill:none;stroke-width:17;stroke-miterlimit:10;}

      .whell-ic-cell-red.s5 { stop-color: lighten($red,18%); }
      .whell-ic-cell-red.s4 { stop-color: lighten($red,14%); }
      .whell-ic-cell-red.s3 { stop-color: lighten($red,7%); }
      .whell-ic-cell-red.s2 { stop-color: lighten($red,2.5%); }
      .whell-ic-cell-red.s1 { stop-color: $red; }
    }
    .outside {
      width:100%;
      height:100%;
      display:block;
      position:relative;
      .gradient.g1 {stop-color:var(--v-primary-base);stop-opacity: 0.00;}
      .gradient.g2 {stop-color:var(--v-primary-base);stop-opacity: 0.00;}
      .gradient.g3 {stop-color:var(--v-primary-base);stop-opacity: 0.10;}
      .gradient.g4 {stop-color:var(--v-primary-base);stop-opacity: 0.25;}
      .gradient.g5 {stop-color:var(--v-primary-base);}
      .st0{stroke:var(--v-primary-base);}
    }
  }

  .bets {
    .bets-sup {
      z-index:2;
      position: absolute;
      height:0;
      margin-left:calc( #{$width} + #{$margin});
      @media screen and (min-width: 576px) { margin-left:calc( #{$width576} + #{$margin}); }
      @media screen and (min-width: 768px) { margin-left:calc( #{$width768} + #{$margin}); }
      @media screen and (min-width: 992px) { margin-left:calc( #{$width992} + #{$margin}); }
      @media screen and (min-width: 1200px) { margin-left:calc( #{$width1200} + #{$margin}); }
      .betm {
        position: relative;
        width:$width;
        height:calc( #{$width}/2.2 );
        float:left;
        margin-right:$margin;
        transform: translate(-50%,-50%);
        cursor: pointer;
        outline:none;
        &.vt {top:calc( -#{$margin}/2 );}
        &.vm {top:calc( #{$width}/2 );}
        &.v2 {top:calc( #{$width}*1.5 + #{$margin} );width:calc( #{$width}/2.2 );height:calc(#{$width}*2 + #{$margin}*1);}
        &.hr {left:calc( #{$width} + #{$margin}/2 );}
        &.hl {left:calc( -#{$margin}/2 );}
        &.hm {left:calc( #{$width}/2 );}
        &.szc {width:calc( #{$width}/2.2 );margin-right:calc( (#{$width} - #{$width}/2.2) + #{$margin} );}
        &.szhs {width:calc( #{$width}/2.2 );height:calc( #{$width}/1.5 );margin-right:calc( (#{$width} - #{$width}/2.2) + #{$margin} );}
        &.szvs {width:calc( #{$width}/1.5 );height:calc( #{$width}/2.2 );margin-right:calc( (#{$width} - #{$width}/1.5) + #{$margin} );}
        &:after{
          content:attr(data-betsize);
          position: absolute;
          height: 20px;
          min-width: 20px;
          border-radius: 10px;
          font-size: 12px;
          background-color: var(--v-primary-base);
          padding: 4px 6px;
          @media screen and (max-width: 992px) { font-size: 8px;height: 16px;min-width: 16px; }
          color: #FFFFFF;
          text-align: center;
          transform:translate(-50%,-50%) scale(0);
          transform-origin:center;
          transition:.3s;
          letter-spacing: 0;
          line-height: 1;
          opacity:0;
          top:50%;
          left:50%;
          z-index:10;
        }
        &[data-betsize]:after {
          transform:translate(-50%,-50%) scale(1);
          opacity:1
        }

        @media screen and (min-width: 576px) {
          width:$width576;
          height:calc( #{$width576}/2.2 );
          &.vm {top:calc( #{$width576}/2 );}
          &.v2 {top:calc( #{$width576}*1.5 + #{$margin} );width:calc( #{$width576}/2.2 );height:calc(#{$width576}*2 + #{$margin}*1);}
          &.hr {left:calc( #{$width576} + #{$margin}/2 );}
          &.hm {left:calc( #{$width576}/2 );}
          &.szc {width:calc( #{$width576}/2.2 );margin-right:calc( (#{$width576} - #{$width576}/2.2) + #{$margin} );}
          &.szhs {width:calc( #{$width576}/2.2 );height:calc( #{$width576}/1.5 );margin-right:calc( (#{$width576} - #{$width576}/2.2) + #{$margin} );}
          &.szvs {width:calc( #{$width576}/1.5 );height:calc( #{$width576}/2.2 );margin-right:calc( (#{$width576} - #{$width576}/1.5) + #{$margin} );}
        }
        @media screen and (min-width: 768px) {
          width:$width768;
          height:calc( #{$width768}/2.2 );
          &.vm {top:calc( #{$width768}/2 );}
          &.v2 {top:calc( #{$width768}*1.5 + #{$margin} );width:calc( #{$width768}/2.2 );height:calc(#{$width768}*2 + #{$margin}*1);}
          &.hr {left:calc( #{$width768} + #{$margin}/2 );}
          &.hm {left:calc( #{$width768}/2 );}
          &.szc {width:calc( #{$width768}/2.2 );margin-right:calc( (#{$width768} - #{$width768}/2.2) + #{$margin} );}
          &.szhs {width:calc( #{$width768}/2.2 );height:calc( #{$width768}/1.5 );margin-right:calc( (#{$width768} - #{$width768}/2.2) + #{$margin} );}
          &.szvs {width:calc( #{$width768}/1.5 );height:calc( #{$width768}/2.2 );margin-right:calc( (#{$width768} - #{$width768}/1.5) + #{$margin} );}
        }
        @media screen and (min-width: 992px) {
          width:$width992;
          height:calc( #{$width992}/2.2 );
          &.vm {top:calc( #{$width992}/2 );}
          &.v2 {top:calc( #{$width992}*1.5 + #{$margin} );width:calc( #{$width992}/2.2 );height:calc(#{$width992}*2 + #{$margin}*1);}
          &.hr {left:calc( #{$width992} + #{$margin}/2 );}
          &.hm {left:calc( #{$width992}/2 );}
          &.szc {width:calc( #{$width992}/2.2 );margin-right:calc( (#{$width992} - #{$width992}/2.2) + #{$margin} );}
          &.szhs {width:calc( #{$width992}/2.2 );height:calc( #{$width992}/1.5 );margin-right:calc( (#{$width992} - #{$width992}/2.2) + #{$margin} );}
          &.szvs {width:calc( #{$width992}/1.5 );height:calc( #{$width992}/2.2 );margin-right:calc( (#{$width992} - #{$width992}/1.5) + #{$margin} );}
        }
        @media screen and (min-width: 1200px) {
          width:$width1200;
          height:calc( #{$width1200}/2.2 );
          &.vm {top:calc( #{$width1200}/2 );}
          &.v2 {top:calc( #{$width1200}*1.5 + #{$margin} );width:calc( #{$width1200}/2.2 );height:calc(#{$width1200}*2 + #{$margin}*1);}
          &.hr {left:calc( #{$width1200} + #{$margin}/2 );}
          &.hm {left:calc( #{$width1200}/2 );}
          &.szc {width:calc( #{$width1200}/2.2 );margin-right:calc( (#{$width1200} - #{$width1200}/2.2) + #{$margin} );}
          &.szhs {width:calc( #{$width1200}/2.2 );height:calc( #{$width1200}/1.5 );margin-right:calc( (#{$width1200} - #{$width1200}/2.2) + #{$margin} );}
          &.szvs {width:calc( #{$width1200}/1.5 );height:calc( #{$width1200}/2.2 );margin-right:calc( (#{$width1200} - #{$width1200}/1.5) + #{$margin} );}
        }

      }
    }
    .bet-group {
      z-index:1;
      position: relative;
      width:$width;
      height:$width;
      margin-right:$margin;
      margin-bottom:0;
      float:left;
      box-sizing: border-box;
      &.v3{height:calc(#{$width}*3 + #{$margin}*2);}
      @media screen and (min-width: 576px) {
          width:$width576;height: $width576;
          &.v3{height:calc(#{$width576}*3 + #{$margin}*2);}
      }
      @media screen and (min-width: 768px) {
          width:$width768;height: $width768;
          &.v3{height:calc(#{$width768}*3 + #{$margin}*2);}
      }
      @media screen and (min-width: 992px) {
          width:$width992;height: $width992;
          &.v3{height:calc(#{$width992}*3 + #{$margin}*2);}
      }
      @media screen and (min-width: 1200px) {
          width:$width1200;height: $width1200;
          &.v3{height:calc(#{$width1200}*3 + #{$margin}*2);}
      }
    }
    .bet {
      z-index:1;
      position: relative;
      width:$width;
      height:$width;
      margin-right:$margin;
      margin-bottom:$margin;
      float:left;
      font-size: 0.875rem;
      box-sizing: border-box;
      line-height: calc( #{$width} - 2px);
      text-align: center;
      cursor: pointer;
      transition: border-color .3s,background-color .3s,color .3s;
      color: var(--v-primary-base);
      &.theme--dark.v-btn {
        color:#fff;
      }
      caret-color: var(--v-primary-base);
      border: thin solid currentColor;
      border-radius: 4px;
      outline: none;
      &:before {
        content: "";
        border-radius: inherit;
        bottom: 0;
        color: inherit;
        left: 0;
        opacity: 0;
        pointer-events: none;
        position: absolute;
        right: 0;
        top: 0;
        transition: opacity 0.2s cubic-bezier(0.4, 0, 0.6, 1);
        background-color: currentColor;
      }
      &:hover:before, &.hover:before {
        opacity:0.2;
      }
      &:active:before, &.active:before {
        opacity:0.4;
      }
      > span {
        position:relative;
        z-index:1;
      }
      &.red, &.red.win:active, &.red.win:hover, &.red.win.active, &.red.win.hover { background: $red !important; color:#fafafa; border:none;color:#fff;}
      &.black, &.black.win:active, &.black.win:hover, &.black.win.active, &.black.win.hover {background:black !important; color:#fafafa; border:none;color:#fff;}
      &.win { background: var(--v-primary-base) !important;color:#fafafa; border-color: var(--v-primary-base) !important}
      @media screen and (max-width: 992px) { font-size: 0.7rem; }
      @media screen and (max-width: 768px) { font-size: 0.5rem; }
      @media screen and (max-width: 430px) { font-size: 0.4rem;  }
      &.v15{height:calc(#{$width}*1.5 + #{$margin}*0.5);line-height: calc( #{$width}*1.5 + #{$margin}*0.5 - 2px);}
      &.v3{height:calc(#{$width}*3 + #{$margin}*2);line-height: calc( #{$width}*3 + #{$margin}*2 - 2px);}
      &.ml1{margin-left:calc( #{$width} + #{$margin} );}
      &.h2{width:calc(#{$width}*2 + #{$margin}*1);}
      &.h4{width:calc(#{$width}*4 + #{$margin}*3);}
      &.bnm{margin-bottom:0;}
      &:after{
          content:attr(data-betsize);
          position: absolute;
          // width: calc( #{$width}/2.2 );
          // height: calc( #{$width}/3 );
          height: 20px;
          min-width: 20px;
          border-radius: 10px;
          font-size: 12px;
          background-color: var(--v-primary-base);
          padding: 4px 6px;
          @media screen and (max-width: 992px) { font-size: 8px;height: 16px;min-width: 16px; }
          color: #FFFFFF;
          text-align: center;
          bottom:1px;
          transform:scale(0);
          transform-origin:center;
          transition:.3s;
          letter-spacing: 0;
          // line-height: calc( #{$width}/3 );
          line-height: 1;
          opacity:0;
          top:0;
          right:0;
          z-index:10;
      }
      &[data-betsize]:after {
          transform:scale(1);
          opacity:1;
      }
      @media screen and (min-width: 576px) {
        width:$width576; height: $width576; line-height: calc( #{$width576} - 2px);
        &.v15{height:calc(#{$width576}*1.5 + #{$margin}*0.5);line-height: calc( #{$width576}*1.5 + #{$margin}*0.5 - 2px);}
        &.v3{height:calc(#{$width576}*3 + #{$margin}*2);line-height: calc( #{$width576}*3 + #{$margin}*2 - 2px);}
        &.ml1{margin-left:calc( #{$width576} + #{$margin} );}
        &.h2{width:calc(#{$width576}*2 + #{$margin}*1);}
        &.h4{width:calc(#{$width576}*4 + #{$margin}*3);}
      }
      @media screen and (min-width: 768px) {
        width:$width768; height: $width768; line-height: calc( #{$width768} - 2px);
        &.v15{height:calc(#{$width768}*1.5 + #{$margin}*0.5);line-height: calc( #{$width768}*1.5 + #{$margin}*0.5 - 2px);}
        &.v3{height:calc(#{$width768}*3 + #{$margin}*2);line-height: calc( #{$width768}*3 + #{$margin}*2 - 2px);}
        &.ml1{margin-left:calc( #{$width768} + #{$margin} );}
        &.h2{width:calc(#{$width768}*2 + #{$margin}*1);}
        &.h4{width:calc(#{$width768}*4 + #{$margin}*3);}
      }
      @media screen and (min-width: 992px) {
        width:$width992; height: $width992; line-height: calc( #{$width992} - 2px);
        &.v15{height:calc(#{$width992}*1.5 + #{$margin}*0.5);line-height: calc( #{$width992}*1.5 + #{$margin}*0.5 - 2px);}
        &.v3{height:calc(#{$width992}*3 + #{$margin}*2);line-height: calc( #{$width992}*3 + #{$margin}*2 - 2px);}
        &.ml1{margin-left:calc( #{$width992} + #{$margin} );}
        &.h2{width:calc(#{$width992}*2 + #{$margin}*1);}
        &.h4{width:calc(#{$width992}*4 + #{$margin}*3);}
      }
      @media screen and (min-width: 1200px) {
        width:$width1200; height: $width1200; line-height: calc( #{$width1200} - 2px);
        &.v15{height:calc(#{$width1200}*1.5 + #{$margin}*0.5);line-height: calc( #{$width1200}*1.5 + #{$margin}*0.5 - 2px);}
        &.v3{height:calc(#{$width1200}*3 + #{$margin}*2);line-height: calc( #{$width1200}*3 + #{$margin}*2 - 2px);}
        &.ml1{margin-left:calc( #{$width1200} + #{$margin} );}
        &.h2{width:calc(#{$width1200}*2 + #{$margin}*1);}
        &.h4{width:calc(#{$width1200}*4 + #{$margin}*3);}
      }
      &.filler {
        background:none !important;
        border:none !important;
        cursor: default !important;
      }
    }
    &.bet-ready{
      .bet {
        button.hover:before  { opacity:0.2;background: #fff; }
        button.active:before  { opacity:1; background: var(--v-primary-base); }
        button.active.primary--text  { color: #fff !important; }
        button.red.hover:before  { opacity:0.3;background: #fff; }
        button.red.active:before  { opacity:1; background: var(--v-primary-base); }
        button.black.hover:before  { opacity:0.2;background: #fff; }
        button.black.active:before  { opacity:1; background: var(--v-primary-base); }
      }
    }
    .fixer {
      clear: both;
    }
    .break{
      height:$width;display:block;margin-bottom:$margin;
      @media screen and (min-width: 576px) { height:$width576; }
      @media screen and (min-width: 768px) { height:$width768; }
      @media screen and (min-width: 992px) { height:$width992; }
      @media screen and (min-width: 1200px) { height:$width1200; }
    }
    @media screen and (max-width: 575px) { margin-bottom:16px; }
  }
.message-container {
    height: 0;
    padding: 32px 0 10px 0;;
  }
</style>
