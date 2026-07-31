import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M6 4H4a2 2 0 0 0-2 2v1a4 4 0 0 0 4 4m0-7V2h12v2m0 0h2a2 2 0 0 1 2 2v1a4 4 0 0 1-4 4m0-7v3a6 6 0 0 1-12 0V4m6 10v4m-3 0h6m-3-4a6 6 0 0 0 6-6V4H6v4a6 6 0 0 0 6 6Z"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
}
