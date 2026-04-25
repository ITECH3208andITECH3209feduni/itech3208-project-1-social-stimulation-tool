import Image from "next/image";
import Link from "next/link";
import {cn} from "../../lib/utils";

interface LogoProps {
    className?: string;
};

const Logo = ({className}: LogoProps ) => {
    return(
        <Link href={"/"}>
            <Image
                src={"/images/federation-logo.png"}
                alt="Federation University logo"
                width={250}
                height={80}
                priority //should be loaded earlier because it's in the header     
                className="w-[300px] h-auto"
                />          
        </Link>
    )
}
Logo.displayName = "Logo"
export {Logo};