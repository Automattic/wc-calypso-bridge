declare global {
	interface Window {
		wcTracks: {
			isEnabled: boolean;
			recordEvent: ( eventName: string, eventProperties?: object ) => void;
		};
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		wc: any;
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		wp: any;
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		wcCalypsoBridge: any;
		location: Location;
	}
}


export {};
