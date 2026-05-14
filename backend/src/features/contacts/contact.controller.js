import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import ContactService from "./contact.service.js";
import ContactMessages from "./contact.message.js";

const ContactController = {
    // POST /contacts
    sendContact: async (req, res) => {
        try {
            const userId = req.user?._id || req.user?.id;
            const { categoryId, subject, message, acceptedTerms } = req.body;

            const result = await ContactService.sendContact({
                userId,
                categoryId,
                subject,
                message,
                acceptedTerms,
            });

            return resUtil.sendSuccess({
                res,
                statusCode: StatusCodes.CREATED,
                message: ContactMessages.success.SEND_CONTACT_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[ContactController.sendContact]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // GET /contacts (Admin)
    getContacts: async (req, res) => {
        try {
            const { page, limit, status } = req.query;

            const result = await ContactService.getContacts({
                page: parseInt(page) || 1,
                limit: parseInt(limit) || 20,
                status,
            });

            return resUtil.sendSuccess({
                res,
                message: ContactMessages.success.GET_CONTACTS_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[ContactController.getContacts]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // PATCH /contacts/:id/status (Admin)
    updateStatus: async (req, res) => {
        try {
            const { id } = req.params;
            const { status } = req.body;

            const result = await ContactService.updateStatus(id, status);

            return resUtil.sendSuccess({
                res,
                message: ContactMessages.success.UPDATE_CONTACT_STATUS_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[ContactController.updateStatus]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default ContactController;
