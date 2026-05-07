import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import FeedbackService from "./feedback.service.js";
import FeedbackMessages from "./feedback.message.js";

const FeedbackController = {
    // POST /feedbacks
    createFeedback: async (req, res) => {
        try {
            const userId = req.user?._id || req.user?.id;
            const { videoId, parentId, content, rating } = req.body;

            const newFeedback = await FeedbackService.createFeedback({
                userId,
                videoId,
                parentId,
                content,
                rating,
            });

            return resUtil.sendSuccess({
                res,
                statusCode: StatusCodes.CREATED,
                message: FeedbackMessages.success.CREATE_FEEDBACK_SUCCESSFULLY,
                data: newFeedback,
            });
        } catch (error) {
            loggerUtil.error(`[FeedbackController.createFeedback]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // GET /feedbacks/:videoId
    getFeedbacksByVideo: async (req, res) => {
        try {
            const { videoId } = req.params;
            const { page, limit } = req.query;

            const result = await FeedbackService.getFeedbacksByVideo(videoId, {
                page: parseInt(page) || 1,
                limit: parseInt(limit) || 20,
            });

            return resUtil.sendSuccess({
                res,
                message: FeedbackMessages.success.GET_FEEDBACKS_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[FeedbackController.getFeedbacksByVideo]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // DELETE /feedbacks/:id
    deleteFeedback: async (req, res) => {
        try {
            const userId = req.user?._id || req.user?.id;
            const feedbackId = req.params.id;

            const result = await FeedbackService.deleteFeedback(feedbackId, userId);

            return resUtil.sendSuccess({
                res,
                message: FeedbackMessages.success.DELETE_FEEDBACK_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[FeedbackController.deleteFeedback]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default FeedbackController;
