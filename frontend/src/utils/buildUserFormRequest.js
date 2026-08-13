import loggerUtil from "./logger.utils";

const buildAvatarFormData = ({ avatar }) => {
    const formData = new FormData();
    if (avatar instanceof File || avatar instanceof Blob) formData.append("avatar", avatar);

    // for (const [k, v] of formData.entries()) {
    //     loggerUtil.debug(k, v);
    // }

    return formData;
};

const buildProfilePayload = ({ firstName, lastName, email, location }) => ({
    firstName,
    lastName,
    email,
    location,
});

const userFormRequest = { buildAvatarFormData, buildProfilePayload };

export default userFormRequest;
