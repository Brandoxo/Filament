<div class="relative mb-6 overflow-hidden rounded-2xl"
     style="
            border: 1px solid rgba(183,152,87,0.35);
            box-shadow: 0 1px 3px rgba(42,40,38,0.06), inset 0 1px 0 rgba(255,255,255,0.8);">

    {{-- Borde decorativo superior --}}
    <div class="absolute inset-x-0 top-0 h-[3px]"
         style="background: linear-gradient(90deg, transparent 0%, #b79857 25%, #c05621 50%, #b79857 75%, transparent 100%);"></div>

    {{-- Patrón de fondo sutil --}}
    <div class="absolute inset-0 opacity-[0.03]"
         style="background-image: repeating-linear-gradient(45deg, #2a2826 0px, #2a2826 1px, transparent 1px, transparent 8px);"></div>

    <div class="relative px-6 py-8 text-center sm:px-10">

        {{-- Ornamento superior --}}
        <p class="text-sm tracking-[0.4em]" style="color: #b79857;">❧ ✦ ❧</p>

        {{-- Eyebrow --}}
        <p class="mt-2 text-[0.65rem] font-semibold uppercase tracking-[0.35em]"
           style="color: #8c8c88; letter-spacing: 0.3em;">
            Panel de Administración
        </p>

        {{-- Línea decorativa --}}
        <div class="mx-auto mt-3 flex max-w-xs items-center gap-3">
            <span class="h-px flex-1" style="background: rgba(183,152,87,0.45);"></span>
            <span style="color: #b79857; font-size: 0.6rem;">◆</span>
            <span class="h-px flex-1" style="background: rgba(183,152,87,0.45);"></span>
        </div>

        {{-- Título principal --}}
        <h1 class="mt-3 text-3xl font-bold sm:text-4xl"
            style="font-family: ui-serif, Georgia, Cambria, 'Times New Roman', serif;
                   color: #2a2826;
                   letter-spacing: -0.02em;
                   line-height: 1.15;">
            Bazar de Ropa
        </h1>

        {{-- Subtítulo vintage --}}
        <div class="mt-2 flex items-center justify-center gap-4">
            <span class="h-px w-16 sm:w-24" style="background: rgba(183,152,87,0.4);"></span>
            <p class="text-sm italic" style="font-family: ui-serif, Georgia, Cambria, serif; color: #c05621;">
                Est. 2024 &middot; Colección Vintage
            </p>
            <span class="h-px w-16 sm:w-24" style="background: rgba(183,152,87,0.4);"></span>
        </div>

        {{-- Fecha --}}
        <p class="mt-4 text-xs tracking-widest" style="color: #8c8c88; letter-spacing: 0.12em;">
            {{ ucfirst(\Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY')) }}
        </p>

        {{-- Ornamento inferior --}}
        <p class="mt-3 text-sm tracking-[0.4em]" style="color: #b79857;">❧ ✦ ❧</p>

    </div>

    {{-- Borde decorativo inferior --}}
    <div class="absolute inset-x-0 bottom-0 h-[3px]"
         style="background: linear-gradient(90deg, transparent 0%, #b79857 25%, #c05621 50%, #b79857 75%, transparent 100%);"></div>
</div>
