<template>
    <div class="mb-2">
        <button class="text-lg font-bold text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleBold().run()" :class="{ 'is-active': editor.isActive('bold') }">
            <i class="fas Fa-Bold">B</i>
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'is-active': editor.isActive('italic') }">
            <i class="fas Fa-Italic italic font-light">I</i>
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'is-active': editor.isActive('strike') }">
            <i class="fas Fa-Strikethrough line-through font-light">S</i>
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleCode().run()" :class="{ 'is-active': editor.isActive('code') }">
            <i class="fas fa-code"></i>
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().setParagraph().run()" :class="{ 'is-active': editor.isActive('paragraph') }">
            <i class="fas Fa-Paragraph">P</i>
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'is-active': editor.isActive('blockquote') }">
            <i class="fas Fa-Quote-Left font-bold">“</i>
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }">
            <i class="fas fa-Heading">H</i>1
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }">
            <i class="fas fa-Heading">H</i>2
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }">
            <i class="fas fa-Heading">H</i>3
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'is-active': editor.isActive('bulletList') }">
            <i class="fas fa-List-Ul"></i>Bl
        </button>
        <button class="text-lg text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'is-active': editor.isActive('orderedList') }">
            <i class="fas fa-List-Ol"></i>Ol
        </button>
        <button class="text-sm text-we4vGrey-700 px-2 mr-1 rounded-md border border-we4vGrey-300 bg-we4vGrey-100 tracking-tight hover:bg-we4vGrey-200 cursor-pointer min-h-menu-button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'is-active': editor.isActive('codeBlock') }">
            <i class="fas fa-File-Code"></i>codeblock
        </button>
    </div>
</template>

<script>

export default {
    name: 'MenuBar',

    props: [
        'editor'
    ]
}
</script>