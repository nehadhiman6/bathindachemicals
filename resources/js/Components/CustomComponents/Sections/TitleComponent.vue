<template>
    <div class="flex flex-wrap justify-between items-center mb-5">
        <h4 class="font-bold text-lg pl-5 border-l-4 border-sky-500 mb-5">
                <slot name="title" />
        </h4>
        <p class="flex flex-wrap items-center mt-1 text-sm text-gray-600">
            <slot name="right-content" />
        </p>
    </div>
</template>
