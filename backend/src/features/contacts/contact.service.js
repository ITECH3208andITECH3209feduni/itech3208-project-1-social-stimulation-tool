import ContactModel from "#models/contact.model.js";
import ContactMessages from "./contact.message.js";

const ContactService = {
    // MARK: - HELPER: FORMAT CONTACT
    _formatContact: (contact) => {
        if (!contact) return null;

        const { _id, userId, categoryId, __v, ...rest } = contact;
        const formatted = { id: _id, ...rest };

        if (userId) {
            const { _id: uId, ...uRest } = userId;
            formatted.user = { id: uId, ...uRest };
        }

        if (categoryId) {
            const { _id: cId, ...cRest } = categoryId;
            formatted.category = { id: cId, ...cRest };
        }

        return formatted;
    },

    // MARK: - SEND CONTACT
    sendContact: async ({ userId, categoryId, subject, message, acceptedTerms }) => {
        const newContact = await ContactModel.create({
            userId,
            categoryId,
            subject,
            message,
            acceptedTerms,
        });

        const contact = await ContactModel.findById(newContact._id)
            .populate("userId", "username email")
            .populate("categoryId", "name")
            .lean();

        return ContactService._formatContact(contact);
    },

    // MARK: - GET CONTACTS (Admin)
    getContacts: async ({ page = 1, limit = 20, status } = {}) => {
        const skip = (page - 1) * limit;
        const query = status ? { status } : {};

        const [contacts, total] = await Promise.all([
            ContactModel.find(query)
                .populate("userId", "username email avatar")
                .populate("categoryId", "name")
                .sort({ createdAt: -1 })
                .skip(skip)
                .limit(limit)
                .lean(),
            ContactModel.countDocuments(query),
        ]);

        return {
            contacts: contacts.map((c) => ContactService._formatContact(c)),
            pagination: {
                total,
                page,
                limit,
                totalPages: Math.ceil(total / limit),
            },
        };
    },

    // MARK: - UPDATE STATUS (Admin)
    updateStatus: async (contactId, status) => {
        const contact = await ContactModel.findById(contactId)
            .populate("userId", "username email")
            .populate("categoryId", "name");

        if (!contact) {
            throw ContactMessages.error.CONTACT_NOT_FOUND();
        }

        contact.status = status;
        await contact.save();

        return ContactService._formatContact(contact.toObject());
    },
};

export default ContactService;
