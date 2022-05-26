import Paragraph from '@tiptap/extension-paragraph'

export default Paragraph.extend({
    renderHTML({ HTMLAttributes }) {
        HTMLAttributes.class = 'mb-2 text-we4vGrey-600 text-sm'

        return ['p', HTMLAttributes, 0]
    }
})