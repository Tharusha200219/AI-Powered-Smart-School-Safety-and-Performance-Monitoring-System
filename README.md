# AudioBasedThreatDetection-Models

Small utilities and models for building audio-based threat detection datasets and training simple classifiers.

## Important: raw audio files are excluded from the repository
To avoid committing large or sensitive recordings, the repository now ignores raw audio files under `data/raw/` via `.gitignore`.

If you need to keep an empty folder in source control, add a `.gitkeep` file to `data/raw/` and it will be preserved.

If raw audio files were already committed, remove them from git history with:

```powershell
git rm --cached -r data/raw
git commit -m "Remove raw audio from repo; add to .gitignore"
```

## Quick start

1. Install dependencies (example, use a virtual environment):

```powershell
python -m venv .venv; .\.venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

If you don't have a `requirements.txt`, the main dependencies are:
- numpy
- librosa

2. Create spectrograms from raw audio (saves `.npy` mel-spectrograms under `data/processed/<label>/`):

```powershell
python make_spectrograms.py
```

3. Train models (example script):

```powershell
python train_cnn.py
```

## Notes and next steps
- Remove unused imports (e.g. `matplotlib.pyplot` in `make_spectrograms.py`) to tidy code.
- Add `requirements.txt` and a small CLI to `make_spectrograms.py` for configurable paths/lengths.
- Consider adding a `.gitkeep` to `data/raw/` if you want the empty folder tracked.

If you'd like, I can add `requirements.txt` and a basic CLI for the spectrogram script next.