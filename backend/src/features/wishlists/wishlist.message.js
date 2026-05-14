import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    ADD_TO_WISHLIST_SUCCESSFULLY: "Video added to wishlist successfully.",
    REMOVE_FROM_WISHLIST_SUCCESSFULLY: "Video removed from wishlist successfully.",
    GET_WISHLIST_SUCCESSFULLY: "Get wishlist successfully.",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    WISHLIST_ITEM_NOT_FOUND: () =>
        createError({
            message: "This item is not in your wishlist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "WISHLIST_ITEM_NOT_FOUND",
        }),
};

const WishlistMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
};

export default WishlistMessages;
