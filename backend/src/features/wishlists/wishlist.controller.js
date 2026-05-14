import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import WishlistService from "./wishlist.service.js";
import WishlistMessages from "./wishlist.message.js";

const WishlistController = {
    // PATCH /wishlists/toggle/:videoId
    toggleWishlist: async (req, res) => {
        try {
            const userId = req.user?._id || req.user?.id;
            const videoId = req.params.videoId;

            const { isAdded } = await WishlistService.toggleWishlist({ userId, videoId });

            return resUtil.sendSuccess({
                res,
                message: isAdded
                    ? WishlistMessages.success.ADD_TO_WISHLIST_SUCCESSFULLY
                    : WishlistMessages.success.REMOVE_FROM_WISHLIST_SUCCESSFULLY,
                data: { isAdded },
            });
        } catch (error) {
            loggerUtil.error(`[WishlistController.toggleWishlist]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // GET /wishlists
    getWishlist: async (req, res) => {
        try {
            const userId = req.user?._id || req.user?.id;
            const { page, limit } = req.query;

            const result = await WishlistService.getWishlist(userId, {
                page: parseInt(page) || 1,
                limit: parseInt(limit) || 10,
            });

            return resUtil.sendSuccess({
                res,
                message: WishlistMessages.success.GET_WISHLIST_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[WishlistController.getWishlist]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default WishlistController;
